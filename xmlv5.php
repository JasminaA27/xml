<?php

$conexion = new mysqli("localhost", "root", "root");

$conexion->query("CREATE DATABASE iesp22_bd");

$conexion = new mysqli("localhost", "root", "root", "iesp22_bd");

// Primero crear las tablas sin las restricciones de clave foránea
$conexion->query("CREATE TABLE sigi_programa_estudios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(10) NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL
)");


$conexion->query("CREATE TABLE sigi_planes_estudio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_programa INT NOT NULL,
    nombre VARCHAR(20) NOT NULL,
    resolucion VARCHAR(100) NOT NULL,
    fecha_registro DATETIME NOT NULL,
    perfil_egresado TEXT NOT NULL
)");


$conexion->query("CREATE TABLE sigi_modulo_formativo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion TEXT NOT NULL,
    nro_modulo INT NOT NULL,
    id_plan INT NOT NULL
)");


$conexion->query("CREATE TABLE sigi_semestre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(5) NOT NULL,
    id_modulo INT NOT NULL
)");


$conexion->query("CREATE TABLE sigi_unidad_didactica (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    id_semestre INT NOT NULL,
    creditos_teorico INT NOT NULL,
    creditos_practico INT NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    horas_semanal INT,
    horas_semestral INT,
    orden INT NOT NULL
)");

echo "Base de datos y tablas creadas exitosamente<br><br>";

// 1. Relación entre planes_estudio y programa_estudios
$conexion->query("ALTER TABLE sigi_planes_estudio 
    ADD CONSTRAINT fk_planes_programa 
    FOREIGN KEY (id_programa) 
    REFERENCES sigi_programa_estudios(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE");

// 2. Relación entre modulo_formativo y planes_estudio
$conexion->query("ALTER TABLE sigi_modulo_formativo 
    ADD CONSTRAINT fk_modulo_plan 
    FOREIGN KEY (id_plan) 
    REFERENCES sigi_planes_estudio(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE");

// 3. Relación entre semestre y modulo_formativo
$conexion->query("ALTER TABLE sigi_semestre 
    ADD CONSTRAINT fk_semestre_modulo 
    FOREIGN KEY (id_modulo) 
    REFERENCES sigi_modulo_formativo(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE");

// 4. Relación entre unidad_didactica y semestre
$conexion->query("ALTER TABLE sigi_unidad_didactica 
    ADD CONSTRAINT fk_unidad_semestre 
    FOREIGN KEY (id_semestre) 
    REFERENCES sigi_semestre(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE");

echo "Relaciones creadas correctamente<br><br>";

// Insertar datos del XML
$xml = simplexml_load_file('ies2.xml') or die('Error no se cargo el xml');

foreach ($xml as $i_pe => $pe) {
    echo 'codigo: ' . $pe->codigo . "<br>";
    echo 'tipo: ' . $pe->tipo . "<br>";
    echo 'nombre: ' . $pe->nombre . "<br>";
    
    
    $consulta = "INSERT INTO sigi_programa_estudios (codigo, tipo, nombre) 
                 VALUES ('" . $conexion->real_escape_string($pe->codigo) . "', 
                         '" . $conexion->real_escape_string($pe->tipo) . "', 
                         '" . $conexion->real_escape_string($pe->nombre) . "')";
    $conexion->query($consulta);
    $id_programa = $conexion->insert_id;
    
   
    foreach ($pe->planes_estudio[0] as $i_ple => $plan) {
        echo '--' . $plan->nombre . "<br>";
        echo '--' . $plan->resolucion . "<br>";
        echo '--' . $plan->fecha_registro . "<br>";
        
       
        $consulta = "INSERT INTO sigi_planes_estudio (id_programa, nombre, resolucion, fecha_registro, perfil_egresado) 
             VALUES ($id_programa, 
                    '" . $conexion->real_escape_string($plan->nombre) . "', 
                    '" . $conexion->real_escape_string($plan->resolucion) . "', 
                    '" . $conexion->real_escape_string($plan->fecha_registro) . "', 
                    '" . $conexion->real_escape_string($plan->perfil_egresado) . "')";
        $conexion->query($consulta);
        $id_plan = $conexion->insert_id;
        
       
        foreach ($plan->modulos_formativos[0] as $id_mod => $modulo) {
            echo '----' . $modulo->nro_modulo . "<br>";
            echo '----' . $modulo->descripcion . "<br>";
            
            
            $consulta = "INSERT INTO sigi_modulo_formativo (descripcion, nro_modulo, id_plan) 
                         VALUES ('" . $conexion->real_escape_string($modulo->descripcion) . "', 
                                 " . $modulo->nro_modulo . ", 
                                 $id_plan)";
            $conexion->query($consulta);
            $id_modulo = $conexion->insert_id;
            
           
            foreach ($modulo->periodos[0] as $i_per => $periodo) {
                echo '------' . $periodo->descripcion . "<br>";
                
                
                $consulta = "INSERT INTO sigi_semestre (id_modulo, descripcion) 
                             VALUES ($id_modulo, 
                                    '" . $conexion->real_escape_string($periodo->descripcion) . "')";
                $conexion->query($consulta);
                $id_semestre = $conexion->insert_id;
                
                
                $orden = 1;
                foreach ($periodo->unidades_didacticas[0] as $id_ud => $ud) {
                    echo '--------' . $ud->nombre . "<br>";
                    echo '--------' . $ud->creditos_teorico . "<br>";
                    echo '--------' . $ud->creditos_practico . "<br>";
                    echo '--------' . $ud->tipo . "<br>";
                    echo '--------' . $ud->horas_semanal . "<br>";
                    echo '--------' . $ud->horas_semestral . "<br>";
                    
                   
                    $consulta = "INSERT INTO sigi_unidad_didactica (id_semestre, nombre, creditos_teorico, creditos_practico, tipo, horas_semanal, horas_semestral, orden) 
                                 VALUES ($id_semestre, 
                                        '" . $conexion->real_escape_string($ud->nombre) . "', 
                                        " . $ud->creditos_teorico . ", 
                                        " . $ud->creditos_practico . ", 
                                        '" . $conexion->real_escape_string($ud->tipo) . "', 
                                        " . $ud->horas_semanal . ", 
                                        " . $ud->horas_semestral . ", 
                                        $orden)";
                    $conexion->query($consulta);
                    
                    $orden++;
                }
            }
        }
    }
    echo "<hr>";
}

echo "¡Base de datos creada, relaciones establecidas y datos insertados correctamente!";



$conexion->close();
?>