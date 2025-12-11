<?php

// Crea una nueva conexión a la base de datos MySQL
// Parámetros: servidor, usuario, contraseña, nombre_base_datos
$conexion = new mysqli("localhost", "root", "root", "xml");

// Verifica si hubo error en la conexión
if ($conexion->connect_errno) {
    // Muestra mensaje de error con código y descripción
    echo "Fallo al conectar a MySQL: (" . $conexion->connect_errno . ") " . $conexion->connect_error;
}

// Crea un nuevo documento XML
// Parámetros: versión (1.0), codificación (UTF-8 para caracteres especiales)
$xml = new DOMDocument('1.0', 'UTF-8');

// Formatea la salida XML con sangrías y saltos de línea (más legible)
$xml->formatOutput = true;

// Crea el elemento raíz del XML llamado 'programas_estudio'
$et1 = $xml->createElement('programas_estudio');

// Añade el elemento raíz al documento XML
$xml->appendChild($et1);

// Consulta SQL para obtener TODOS los programas de estudios
$consulta = "SELECT * FROM sigi_programa_estudios";

// Ejecuta la consulta en la base de datos
$resultado = $conexion->query($consulta);

// Recorre CADA fila (programa de estudios) del resultado
while ($pe = mysqli_fetch_assoc($resultado)) {
    // Muestra el nombre del programa en pantalla (solo para depuración)
    echo $pe['nombre'] . "<br>";
    
    // Crea un elemento XML para este programa con nombre dinámico: pe_ + id
    // Ejemplo: pe_1, pe_2, etc.
    $num_pe = $xml->createElement('pe_' . $pe['id']);
    
    // Crea elemento 'codigo' con el valor del campo 'codigo' de la base de datos
    $codigo_pe = $xml->createElement('codigo', $pe['codigo']);
    
    // Añade el elemento 'codigo' como HIJO del elemento del programa (pe_X)
    $num_pe->appendChild($codigo_pe);
    
    // Crea elemento 'tipo' con el valor del campo 'tipo'
    $tipo_pe = $xml->createElement('tipo', $pe['tipo']);
    
    // Añade 'tipo' como hijo de pe_X
    $num_pe->appendChild($tipo_pe);
    
    // Crea elemento 'nombre' con el valor del campo 'nombre'
    $nombre_pe = $xml->createElement('nombre', $pe['nombre']);
    
    // Añade 'nombre' como hijo de pe_X
    $num_pe->appendChild($nombre_pe);


    // ========== NIVEL 2: PLANES DE ESTUDIO ==========
    // Crea elemento CONTENEDOR para todos los planes de estudio
    $et_plan = $xml->createElement('planes_estudio');
    
    // Consulta SQL para obtener los planes de estudio de ESTE programa específico
    // Filtra por id_programa_estudios igual al id del programa actual
    $consulta_plan = "SELECT * FROM sigi_planes_estudio WHERE id_programa_estudios=" . $pe['id'];
    
    // Ejecuta la consulta de planes
    $resultado_plan = $conexion->query($consulta_plan);
    
    // Recorre CADA plan de estudio de este programa
    while ($plan = mysqli_fetch_assoc($resultado_plan)) {
        // Muestra nombre del plan (para depuración)
        echo "--" . $plan['nombre'] . "<br>";
        
        // Crea elemento para este plan con nombre dinámico: plan_ + id
        $num_plan = $xml->createElement('plan_' . $plan['id']);
        
        // Crea elemento 'nombre' para el plan
        $nombre_plan = $xml->createElement('nombre', $plan['nombre']);
        
        // Añade 'nombre' como hijo del plan
        $num_plan->appendChild($nombre_plan);
        
        // Crea elemento 'resolucion' para el plan
        $resolucion_plan = $xml->createElement('resolucion', $plan['resolucion']);
        
        // Añade 'resolucion' como hijo del plan
        $num_plan->appendChild($resolucion_plan);
        
        // Crea elemento 'fecha_registro' para el plan
        $fecha_registro_plan = $xml->createElement('fecha_registro', $plan['fecha_registro']);
        
        // Añade 'fecha_registro' como hijo del plan
        $num_plan->appendChild($fecha_registro_plan);


        // ========== NIVEL 3: MÓDULOS FORMATIVOS ==========
        // Crea elemento CONTENEDOR para todos los módulos
        $et_modulos = $xml->createElement('modulos_formativos');
        
        // Consulta SQL para obtener módulos de ESTE plan específico
        // Filtra por id_plan_estudio igual al id del plan actual
        $consulta_mod = "SELECT * FROM sigi_modulo_formativo WHERE id_plan_estudio=" . $plan['id'];
        
        // Ejecuta la consulta de módulos
        $resultado_mod = $conexion->query($consulta_mod);
        
        // Recorre CADA módulo de este plan
        while ($modulo = mysqli_fetch_assoc($resultado_mod)) {
            // Muestra descripción del módulo (para depuración)
            echo "----" . $modulo['descripcion'] . "<br>";
            
            // Crea elemento para este módulo con nombre dinámico: modulo_ + id
            $num_modulo = $xml->createElement('modulo_' . $modulo['id']);
            
            // Crea elemento 'descripcion' para el módulo
            $descripcion_mod = $xml->createElement('descripcion', $modulo['descripcion']);
            
            // Añade 'descripcion' como hijo del módulo
            $num_modulo->appendChild($descripcion_mod);
            
            // Crea elemento 'nro_modulo' para el módulo
            $nro_modulo_mod = $xml->createElement('nro_modulo', $modulo['nro_modulo']);
            
            // Añade 'nro_modulo' como hijo del módulo
            $num_modulo->appendChild($nro_modulo_mod);
            
            // Crea elemento CONTENEDOR para todos los períodos
            $et_periodos = $xml->createElement('periodos');


            // ========== NIVEL 4: PERÍODOS/SEMESTRES ==========
            // Consulta SQL para obtener períodos de ESTE módulo específico
            // Filtra por id_modulo_formativo igual al id del módulo actual
            $consulta_per = "SELECT * FROM sigi_semestre WHERE id_modulo_formativo=" . $modulo['id'];
            
            // Ejecuta la consulta de períodos
            $resultado_per = $conexion->query($consulta_per);
            
            // Recorre CADA período de este módulo
            while ($per = mysqli_fetch_assoc($resultado_per)) {
                // Muestra descripción del período (para depuración)
                echo "------" . $per['descripcion'] . "<br>";
                
                // Crea elemento para este período con nombre dinámico: periodo_ + id
                $num_per = $xml->createElement('periodo_' . $per['id']);
                
                // Crea elemento 'descripcion' para el período
                $descripcion_per = $xml->createElement('descripcion', $per['descripcion']);
                
                // Añade 'descripcion' como hijo del período
                $num_per->appendChild($descripcion_per);
                
                // Crea elemento CONTENEDOR para todas las unidades didácticas
                $et_uds = $xml->createElement('unidades_didacticas');

                
                // ========== NIVEL 5: UNIDADES DIDÁCTICAS ==========
                // Consulta SQL para obtener unidades de ESTE período específico
                // Filtra por id_semestre igual al id del período actual
                $consulta_uds = "SELECT * FROM sigi_unidad_didactica WHERE id_semestre=" . $per['id'];
                
                // Ejecuta la consulta de unidades
                $resultado_uds = $conexion->query($consulta_uds);
                
                // Recorre CADA unidad didáctica de este período
                while ($uds = mysqli_fetch_assoc($resultado_uds)) {
                    // Muestra nombre de la unidad (para depuración)
                    echo "--------" . $uds['nombre'] . "<br>";
                    
                    // Crea elemento para esta unidad con nombre dinámico: ud_ + orden
                    // NOTA: Usa 'orden' en lugar de 'id' para el nombre
                    $num_ud = $xml->createElement('ud_' . $uds['orden']);
                    
                    // Crea elemento 'nombre' para la unidad
                    $nombre_ud = $xml->createElement('nombre', $uds['nombre']);
                    
                    // Añade 'nombre' como hijo de la unidad
                    $num_ud->appendChild($nombre_ud);


                    // Crea elemento 'creditos_teorico' para la unidad
                    $creditos_teorico = $xml->createElement('creditos_teorico', $uds['creditos_teorico']);
                    
                    // Añade 'creditos_teorico' como hijo de la unidad
                    $num_ud->appendChild($creditos_teorico);


                    // Crea elemento 'creditos_practico' para la unidad
                    $creditos_practico = $xml->createElement('creditos_practico', $uds['creditos_practico']);
                    
                    // Añade 'creditos_practico' como hijo de la unidad
                    $num_ud->appendChild($creditos_practico);
                    
                    // Crea elemento 'tipo' para la unidad
                    $tipo = $xml->createElement('tipo', $uds['tipo']);
                    
                    // Añade 'tipo' como hijo de la unidad
                    $num_ud->appendChild($tipo);

                    
                    // ========== CÁLCULO DE HORAS ==========
                    // Calcula horas semanales: 
                    // (créditos teóricos × 1) + (créditos prácticos × 2)
                    // Cada crédito teórico = 1 hora/semana, práctico = 2 horas/semana
                    $hr_semanal = ($uds['creditos_teorico']*1)+($uds['creditos_practico']*2);
                    
                    // Crea elemento 'horas_semanal' con el valor calculado
                    $hr_sem = $xml->createElement('horas_semanal', $hr_semanal);
                    
                    // Añade 'horas_semanal' como hijo de la unidad
                    $num_ud->appendChild($hr_sem);


                    // Calcula horas semestrales: horas semanales × 16 semanas
                    // Supone un semestre de 16 semanas
                    $hr_semestral = $xml->createElement('horas_semestral', $hr_semanal*16);
                    
                    // Añade 'horas_semestral' como hijo de la unidad
                    $num_ud->appendChild($hr_semestral);
                    
                    // Añade la unidad didáctica completa al contenedor de unidades
                    $et_uds->appendChild($num_ud);
                }
                // Añade el contenedor de unidades al período actual
                $num_per->appendChild($et_uds);
                
                // Añade el período al contenedor de períodos
                $et_periodos->appendChild($num_per);
            }
            // Añade el contenedor de períodos al módulo actual
            $num_modulo->appendChild($et_periodos);
            
            // Añade el módulo al contenedor de módulos
            $et_modulos->appendChild($num_modulo);
        }
        // Añade el contenedor de módulos al plan actual
        $num_plan->appendChild($et_modulos);
        
        // Añade el plan al contenedor de planes
        $et_plan->appendChild($num_plan);
    }
    // Añade el contenedor de planes al programa actual
    $num_pe->appendChild($et_plan);
    
    // Añade el programa al elemento raíz
    $et1->appendChild($num_pe);
}

// Define el nombre del archivo XML de salida
$archivo = "";

// Guarda TODO el documento XML en el archivo especificado
// Crea/sobrescribe el archivo en el directorio actual
$xml->save($archivo);

// FIN DEL SCRIPT - El archivo XML ha sido creado con toda la estructura jerárquica