<?php
$clientsFile = "clients.txt";
$usersFile = "users.txt";

if (!file_exists($clientsFile)) file_put_contents($clientsFile, "");
if (!file_exists($usersFile)) file_put_contents($usersFile, "admin|1234");

$rows = file($clientsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$clients = [];

/* CARGAR CLIENTES */
foreach ($rows as $r) {
    if (!str_contains($r, "|")) continue;
    [$name, $amount, $date] = array_pad(explode("|", $r, 3), 3, "");
    $clients[$name]['vals'][]  = floatval($amount);
    $clients[$name]['dates'][] = $date;
}

/* ORDENAR POR FECHA MÁS RECIENTE */
uasort($clients, function($a, $b){
    return strtotime(end($b['dates'])) <=> strtotime(end($a['dates']));
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Sistema de Gestión</title>

<style>
body{font-family:Segoe UI;background:#f1f5f9;padding:20px}
.container{max-width:1300px;margin:auto}
.card{background:#fff;padding:20px;border-radius:14px;box-shadow:0 8px 20px rgba(0,0,0,.08);margin-bottom:20px}
.header{display:flex;justify-content:space-between;align-items:center}
.status-btn{padding:10px 24px;border:none;border-radius:20px;font-weight:600}
.active{background:#22c55e;color:#fff}
.inactive{background:#ef4444;color:#fff}

.form-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:15px}
input,button{padding:10px;border-radius:10px;border:1px solid #e5e7eb}
button{cursor:pointer}

.client-row{
    display:grid;
    grid-template-columns:2fr 1.2fr 1.2fr 1.2fr 2.5fr;
    gap:20px;
    padding:16px;
    border:1px solid #e5e7eb;
    border-radius:14px;
    margin-bottom:12px;
    align-items:center;
}
.client-name{font-weight:700}
.pos{color:#16a34a;font-weight:600}
.neg{color:#dc2626;font-weight:600}
.balance{font-weight:800;font-size:18px}
.abono{display:flex;gap:10px}

.delete-btn{
    background:#fee2e2;
    color:#b91c1c;
    font-weight:700;
}
.delete-btn:hover{
    background:#dc2626;
    color:#fff;
}
.small{font-size:12px;color:#6b7280}
</style>
</head>

<body>
<div class="container">

<div class="card header">
<h2>Sistema de Gestión de Clientes</h2>
<button class="status-btn inactive" onclick="activate()">🔒 <span id="status">Desactivado</span></button>
</div>

<div class="card">
<form method="POST" action="action.php">
<div class="form-grid">
<input name="client" placeholder="Cliente" required>
<input name="price" type="number" value="0" placeholder="Artículo">
<input name="commission" type="number" value="0" placeholder="Comisión">
<input name="shipping" type="number" value="0" placeholder="Envío">
<button name="add" class="active">Agregar</button>
</div>
</form>
</div>

<?php foreach ($clients as $name=>$data):
    $vals = $data['vals'];
    $dates = $data['dates'];
    $cargos = array_sum(array_filter($vals, fn($v)=>$v>0));
    $abonos = abs(array_sum(array_filter($vals, fn($v)=>$v<0)));
    $balance = $cargos - $abonos;
    $lastDate = end($dates);
?>
<div class="client-row">
    <div class="client-name">
        <?= htmlspecialchars($name) ?>
        <div class="small">Último movimiento: <?= $lastDate ?></div>
    </div>
    <div class="pos">$<?= number_format($cargos,2) ?></div>
    <div class="neg">$<?= number_format($abonos,2) ?></div>
    <div class="balance">$<?= number_format($balance,2) ?></div>

    <form class="abono" method="POST" action="action.php">
        <input type="hidden" name="client" value="<?= htmlspecialchars($name) ?>">
        <input type="number" name="payment" value="0" placeholder="Abono (-)">
        <button name="pay" class="active">Abonar</button>
        <button name="delete" class="delete-btn">Eliminar</button>
    </form>
</div>
<?php endforeach; ?>

</div>

<script>
function activate(){
    let u=prompt("Usuario");
    let p=prompt("Contraseña");
    fetch("auth.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`u=${u}&p=${p}`
    }).then(r=>r.text()).then(res=>{
        if(res==="OK"){
            document.getElementById("status").innerText="Activado";
        }else alert("Credenciales incorrectas");
    });
}
</script>
</body>
</html>
