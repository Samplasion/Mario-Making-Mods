	<table class="{if $post.fulllayout}custompost table{$post.u_id}{else}post{/if} margin" id="post{$post.id}">
		<tr>
			<td class="side userlink{if $post.fulllayout} topbar{$post.u_id}_1{/if}">
				{$post.userlink}
			</td>
			<td class="meta right{if $post.fulllayout} topbar{$post.u_id}_2{/if}">
				<div style="float: left;" id="meta_{$post.id}">
				{if $post.type == $smarty.const.POST_SAMPLE}
					Preview
				{elseif $post.type == $smarty.const.POST_PROFILE}
					About me
				{else}
					{if $post.type == $smarty.const.POST_PM}Sent{else}Posted{/if} on {$post.formattedDate}
					{if $post.threadlink} in {$post.threadlink}{/if}
					{if $post.revdetail} ({$post.revdetail}){/if}
				{/if}
				</div>
				<div style="float: left; text-align:left; display: none;" id="dyna_{$post.id}">
					oops
				</div>
				<ul class="pipemenu">
				{if $post.type == $smarty.const.POST_NORMAL}
					<li><a href="{actionLink page='post' id=$post.id}">Link</a>
					{if $post.links.quote}<li>{$post.links.quote}{/if}
					{if $post.links.edit}<li>{$post.links.edit}{/if}
					{if $post.links.delete}<li>{$post.links.delete}{/if}
					{if $post.links.report}<li>{$post.links.report}{/if}
					{foreach $post.links.extra as $link}
						<li>{$link}
					{/foreach}
				{else if $post.type == $smarty.const.POST_DELETED_SNOOP}
					<li>Post deleted
					{if $post.links.undelete}<li>{$post.links.undelete}{/if}
					{if $post.links.close}<li>{$post.links.close}{/if}
				{/if}
					{if $post.id}<li>#{$post.id}{/if}
					{if $post.ip}<li>{$post.ip}{/if}
				</ul>
			</td>
		</tr>
		<tr>
			<td class="side{if $post.fulllayout} sidebar{$post.u_id}{/if}">
				<div class="smallFonts">
					{if $post.sidebar.rank}{$post.sidebar.rank}<br>{/if}
					{$post.sidebar.title}<br>
					{if $post.sidebar.syndrome}{$post.sidebar.syndrome}<br>{/if}
					{if $post.sidebar.avatar}<br />{$post.sidebar.avatar}<br />{/if}
					<br>
					Level: {$post.sidebar.level}<br />
					EXP: {$post.sidebar.exp}<br />
					Next: {$post.sidebar.next}<br />
					<br />
					{foreach $post.sidebar.extra as $item}
						{if $item}{$item}<br>{/if}
					{/foreach}
					Since: {$post.sidebar.since}<br>
					Posts: {$post.sidebar.posts}<br>
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
