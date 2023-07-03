<table class="listitem" align="left" width="100%" cellpadding="0" cellspacing="0" role="presentation">
	<tr>
		<td align="left">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
				<tr>
					<td align="left">
						<table border="0" cellpadding="0" cellspacing="0" role="presentation">
							<tr>
								<div style="display: flex;
								justify-content: start;
								flex-direction: row;
								vertical-align: top;
								margin-top: 15px;">
									<div style="margin-top: 5px;">
										<a href="{{ $url }}" class="button button-{{ $color ?? 'primary' }}" target="_blank" rel="noopener">#{{ $id }}</a>
									</div>
									<div>
										<p style="margin: 0 0 0 20px;">{{ $slot }}</p>
									</div>
								</div>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
