	<div class="editprofile">
		
		{foreach $pages as $pageid=>$pname}
			<table class="outline margin form eptable" id="{$pageid}">
			
			{foreach $categories.{$pageid} as $catid=>$cname}
				<tr class="header0">
					<th colspan=2>{$cname}</th>
				</tr>
				
				{foreach $fields.{$catid} as $fieldid=>$field}
					<tr class="cell{cycle values='0,1'}">
					
						{if $field.type == 'themeselector'}
						<td class="themeselector" colspan=2>
							{$field.html}
						</td>
						{else}
						<td class="cell2 center" style="width:20%;">
							{$field.caption}
							{if $field.hint}<br><small>{$field.hint}</small>{/if}
						</td>
						<td>
							{$field.html}
						</td>
						{/if}
						
					</tr>
				{/foreach}
				
			{/foreach}
			
			</table>
		{/foreach}
		
		<table class="outline margin form" id="button">
			<tr class="header0">
				<th colspan=2>&nbsp;</th>
			</tr>
			<tr class="cell2">
				<td style="width:20%;"></td>
				<td>
					{$btnEditProfile}
				</td>
			</tr>
		</table>
	
	</div>
