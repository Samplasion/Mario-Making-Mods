{if $post.type == $smarty.const.POST_DEPOT}
<tr class="cell1"><td>
  <div style='display:inline-block;'>{$post.userlink} Posted on {$post.formattedDate}</div>
  <div style='display:inline-block;float:right;'><a href="{actionLink page='depost' id=$post.id}">Link</a>
					{if $post.links.quote} |  {$post.links.quote}  {/if}
					{if $post.links.edit} |  {$post.links.edit}  {/if}
					{if $post.links.delete} |  {$post.links.delete}  {/if}
					{if $post.links.wipe} |  {$post.links.wipe}  {/if}
					{if $post.links.report} |  {$post.links.report}  {/if}
					{foreach $post.links.extra as $link}
						 | {$link}  
					{/foreach}
					{if $post.id} |  #{$post.id}  {/if}
					{if $post.ip} |  {$post.ip}  {/if}</div>
<div style="height: 4px;"></div><div style="padding-left: 12px;">{$post.contents}</div>
</td></tr>
{else}
	<table class="{if $post.fulllayout}custompost table{$post.u_id}{else}post{/if} margin" id="post{$post.id}">
		<tr class="{if $post.fulllayout} topbar{$post.u_id}_3{/if}">
			<td class="side userlink{if $post.fulllayout} topbar{$post.u_id}_1{/if}">
				{$post.userlink}
			</td>
			<td class="meta right{if $post.fulllayout} topbar{$post.u_id}_2{/if}">
				<div style="float: left;" id="meta_{$post.id}">
				{if $post.type == $smarty.const.POST_SAMPLE}
					Preview
				{else}
					{if $post.type == $smarty.const.POST_PM}Sent{else}Posted{/if} on {$post.formattedDate}
					{if $post.threadlink} in {$post.threadlink}{/if}
					{if $post.revdetail} ({$post.revdetail}){/if}
				{/if}
				</div>
				<div style="float: left; text-align:left; display: none;" id="dyna_{$post.id}">
					blarg
				</div>
				<ul class="pipemenu">
				{if $post.type == $smarty.const.POST_NORMAL}
					<li><a href="{actionLink page='post' id=$post.id}">Link</a>
					{if $post.links.quote}<li>{$post.links.quote}</li>{/if}
					{if $post.links.edit}<li>{$post.links.edit}</li>{/if}
					{if $post.links.delete}<li>{$post.links.delete}</li>{/if}
					{if $post.links.wipe}<li>{$post.links.wipe}</li>{/if}
					{if $post.links.report}<li>{$post.links.report}</li>{/if}
					{foreach $post.links.extra as $link}
						<li>{$link}</li>
					{/foreach}
				{else if $post.type == $smarty.const.POST_DELETED_SNOOP}
					<li>Post deleted</li>
					{if $post.links.undelete}<li>{$post.links.undelete}</li>{/if}
					{if $post.links.close}<li>{$post.links.close}</li>{/if}
				{/if}
					{if $post.id}<li>#{$post.id}</li>{/if}
					{if $post.ip}<li>{$post.ip}</li>{/if}
				</ul>
			</td>
		</tr>
		<tr>
			<td class="side{if $post.fulllayout} sidebar{$post.u_id}{/if}">
				<div class="smallFonts">
					{if $post.sidebar.rank}{$post.sidebar.rank}<br>{/if}
					{$post.sidebar.title}<br>
					{if $post.sidebar.syndrome}{$post.sidebar.syndrome}<br>{/if}
					Level: {$post.sidebar.level}<br />
					{$post.sidebar.bar}<br>
					{if $post.sidebar.avatar}{$post.sidebar.avatar}<br>{/if}
					<br>
					Posts: {$post.sidebar.posts}<br>
					EXP: {$post.sidebar.exp}<br />
					Next: {$post.sidebar.next}<br />
					<br />
					Since: {$post.sidebar.since}<br>
					{if $post.sidebar.from} From:  {$post.sidebar.from} <br>{/if}
					{foreach $post.sidebar.extra as $item}
						{if $item}{$item}<br>{/if}
					{/foreach}
					<br>
					Last post: {$post.sidebar.lastpost}<br>
					Last view: {$post.sidebar.lastview}<br>
					User ID:   {$post.sidebar.posterID}<br>
					{$post.sidebar.isonline}
				</div>
			</td>
			<td class="post{if $post.fulllayout} mainbar{$post.u_id}{else if $post.haslayout} haslayout{/if}" id="post_{$post.id}">
				{$post.contents}
			</td>
		</tr>
	</table>
{/if}