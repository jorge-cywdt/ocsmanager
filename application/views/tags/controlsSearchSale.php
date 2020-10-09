<?php
$nameTStation = '';
if ($typeStation == 0) {
    $nameTStation = 'Combustible';
} else if ($typeStation == 1) {
    $nameTStation = 'Market Tienda';
} else if ($typeStation == 2) {
    $nameTStation = 'Market Playa';
} else if ($typeStation == 3) {
    $nameTStation = 'Resumen';
} else if ($typeStation == 4) {
    $nameTStation = 'Estadística';
} else if ($typeStation == 5) {
    $nameTStation = 'Productos por Línea';
}
?>

                <h3><span>Ventas</span> - <span><?php echo $nameTStation ?></span></h3>
                <label>Consultar en:</label>

                <select name="select-station" id="select-station" class="form-control size-text-select">
                    <option value="*">Todas las Estaciones</option>
                    <?php
                    foreach ($result_c_org as $key => $cOrg) {
                        echo '<option value="'.$cOrg->c_org_id.'">'.$cOrg->name.'</option>';
                    }
                    ?>
                </select>
                <br>
                <?php if($typeStation == 4) { ?>
                <label for="">Periodo Anterior:</label>
                <div class="form-group form-group-filled" id="_event_period">
                    <input type="text" class="previous_range form-control" id="_start-date-request" value="<?php echo $previous_start_date; ?>">
                    <input type="text" class="previous_range form-control" id="_end-date-request" value="<?php echo $previous_start_date; ?>">
                </div>
                <?php } ?>
                <?php if($typeStation == 4) { ?>
                <label for="">Periodo Actual:</label>
                <?php } ?>
                <div class="form-group form-group-filled" id="event_period">
                    <input type="text" class="actual_range form-control" id="start-date-request" value="<?php echo $default_start_date; ?>">
                    <input type="text" class="actual_range form-control" id="end-date-request" value="<?php echo $default_start_date; ?>">
                </div>
                

                <input type="hidden" id="qty_sale" value="kardex"><!--kardex y tickets-->
                <input type="hidden" id="type_cost" value="avg"><!--last y avg-->
                <input type="hidden" id="chart-mode" value="0">
                <!--<select id="chart-mode" class="form-control">
                    <option value="0">Gráfico de Barras</option>
                    <option value="1">Gráfico Circular</option>
                </select>-->
                <br>
                <input type="hidden" id="typeStation" value="<?php echo $typeStation ?>">
                <button class="btn btn-primary btn-block btn-search-sale" data-ismarket="<?php echo $typeStation ?>"><span class="glyphicon glyphicon-search"></span> Buscar</button>