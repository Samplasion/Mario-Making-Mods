	<table class="layout-table">
		<tr>
			<td style="width:60%; vertical-align:top; padding-right:0.5em;">
			{foreach $profileParts as $name=>$fields}
				<table class="outline margin profiletable">
					
					{if is_array($fields)}
					
					<tr class="header1">
						<th colspan=2>{$name}</th>
					</tr>
					{foreach $fields as $label=>$val}
						<tr class="cell{cycle values='0,1'}">
							<td class="cell2 center" style="width:20%;">
								{$label}
							</td>
							<td>
								{$val}
							</td>
						</tr>
					{/foreach}
					
					{else}
						
					<tr class="header1">
						<th>{$name}</th>
					</tr>
					<tr class="cell{cycle values='0,1'}">
						<td>
							{$fields}
						</td>
					</tr>
					
					{/if}
					
				</table>
			{/foreach}
			</td>

			<td style="vertical-align:top; padding-left:0.5em;">
				<table class="outline margin" style="width: 275px;">
					<tr class="header1">
						<th colspan=2>RPG Status</th>
					</tr>
					
					<tr class="cell1">
						<td colspan=2>
							<img src="{$rpgstatus}" alt="RPG Status" title="RPG Status for {$username}" />
						</td>
					</tr>
				</table>
				<br />
				<table class="outline margin">
					<tr class="header1">
						<th colspan=2>Equipped Items</th>
					</tr>
							{$equipitems}
				</table>
			</td>
		</tr>
	</table>
	<table class="layout-table">
		<tr>
			<td>
				<table class="outline margin usercomments">
					<tr class="header1">
						<th colspan=2>Profile Comments for {$username}</th>
					</tr>
					
					{if $pagelinks}
					<tr class="cell1">
						<td colspan=2>
							{$pagelinks}
						</td>
					</tr>
					{/if}

					{foreach $comments as $cmt}
					<tr class="cell{cycle values='0,1'}">
						<td class="cell2" style="vertical-align:top; width:20%;">
							{$cmt.userlink}<br>
							<small>{$cmt.formattedDate}</small>
						</td>
						<td style="vertical-align:top;">
							{if $cmt.deleteLink}<small style="float: right; margin: 0px 4px;">{$cmt.deleteLink}</small>{/if}
							{$cmt.text}
						</td>
					</tr>	
					{foreachelse}
					<tr class="cell1">
						<td colspan=2>
							No comments.
						</td>
					</tr>
					{/foreach}

					{if $pagelinks}
					<tr class="cell1">
						<td colspan=2>
							{$pagelinks}
						</td>
					</tr>
					{/if}
					
					{if $commentField}
					<tr class="cell2">
						<td colspan=2>
							{$commentField}
						</td>
					</tr>
					{else if !$loguserid}
					<tr class="cell2">
						<td colspan=2>
							You need to be logged in to post profile comments here.
						</td>
					</tr>
					{/if}
				</table>
			</td>
		</tr>
	</table>
