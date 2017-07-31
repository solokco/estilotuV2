<div class="row" id="Lista_Citas">
	
	<div class="col-sm-8 wpb_column column_container">
		<table>
			
			<thead>
				<tr>
					<th>Servicio</th>
					<th>Usuario</th>
					<th>Pagos pendientes</th>
					<th>Monto</th>
					<th>Status de pago</th>
				</tr>
			
			</thead>
			
			<tbody>
				<?php $total = 0; ?>
				<?php foreach ( $pagos as $pago ): ?>
					
					<?php $user_info = get_userdata( $pago->id_servicio ); ?>
					<?php $total = $total + $pago->monto_pendiente; ?>
					
					<tr>
						<td><?php echo get_the_title( $pago->id_servicio ); ?></td>
						<td><?php echo isset($user_info->first_name) ? $user_info->first_name : "user"; ?></td>
						<td><?php echo $pago->pagos_pendientes; ?></td>
						<td><?php echo $pago->currency; ?> <?php echo $pago->monto_pendiente; ?> </td>
						<td><?php echo ( !isset($user_info->status_pago) || empty($user_info->status_pago) ) ? "Pendiente" : $user_info->status_pago; ?></td>
					</tr>			
				<?php endforeach; ?>
			</tbody>
			
			<tfoot>
				
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td><?php echo $total; ?></td>
					<td></td>
				</tr>
				
			</tfoot>
			
		</table>
	</div>
	
	<div class="col-sm-4 wpb_column column_container">
		
		<div class="row">
			
			<div class="col-sm-6 wpb_column column_container">		
			<?php foreach ($total_generado as $en_moneda): ?>
			
				<h2 class='alert alert-success'><?php echo $en_moneda->currency; ?> <?php echo $en_moneda->monto_pendiente; ?></h2>
			
			<?php endforeach; ?>
			</div>
			
			<div class="col-sm-6 wpb_column column_container">		
			<?php foreach ($total_pendiente as $en_moneda): ?>
			
				<h2 class='alert alert-warning'><?php echo $en_moneda->currency; ?> <?php echo $en_moneda->monto_pendiente; ?></h2>
			
			<?php endforeach; ?>
			</div>
		</div>
		
	</div>
</div>