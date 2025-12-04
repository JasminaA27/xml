<?php
$conexion = new mysqli("localhost", "root", "root","xml");
if ($conexion->connect_errno) {
    echo "Fallo al conectar a MySQL: (" . $conexion->connect_errno . ")" .$conexion->connect_error;
}
$xml = new DOMDocument('1.0','UTF-8');
$xml->formatOutput = true;

$et1 = $xml->createElement('programas_estudio');
$xml->appendChild($et1);

$consulta = "SELECT * FROM sigi_programa_estudios";
$resultado = $conexion->query($consulta);
while ($pe = mysqli_fetch_assoc($resultado)) {
    echo $pe ['nombre']."<br>";
    $num_pe = $xml->createElement('pe_'.$pe['id']);
    $codigo_pe = $xml->createElement('codigo', $pe['codigo']);
    $num_pe->appendChild($codigo_pe);
    $tipo_pe = $xml->createElement('tipo', $pe['tipo']);
    $num_pe->appendChild($tipo_pe);
    $nombre_pe = $xml->createElement('nombre', $pe['nombre']);
    $num_pe->appendChild($nombre_pe);

    $et_plan = $xml->createElement('planes_estudio');
    $consulta_plan = "SELECT * FROM sigi_planes_estudio WHERE id_programa_estudios=".$pe['id'];
    $resultado_plan = $conexion->query($consulta_plan);
    while ($resultados_plan = mysqli_fetch_assoc($resultado_plan)) {

        $plan = $xml->createElement('plan_'.$resultados_plan['id']);

        $nombre_plan = $xml->createElement('nombre', $resultados_plan['nombre']);
        $plan->appendChild($nombre_plan);

        $resolucion_plan = $xml->createElement('resolucion', $resultados_plan['resolucion']);
        $plan->appendChild($resolucion_plan);

        $fecha_registro_plan = $xml->createElement('fecha_registro', $resultados_plan['fecha_registro']);
        $plan->appendChild($fecha_registro_plan);
        
        $et_plan->appendChild($plan);

    }

$num_pe->appendChild($et_plan);
$et1->appendChild($num_pe);
}

$archivo = "ies_db.xml";
$xml->save($archivo);



