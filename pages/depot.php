<?php

MakeCrumbs(array(pageLink('depot') => 'Depot'), $links);

$header = __('Welcome to our depot');
$text = __('Welcome to the Mario Making Mods Depot. Here, you can find the latest and best hacks from our forums!');
$submissions = __('Specify the following in your submission.
				<ul><li>Name of Theme
				<li>Platform (WiiU/3DS)
				<li>Game it replaces (SMB1/SMB3/SMW/NSMBU)
				<li>Screenshots/Video
				<li>Download link</ul>');

RenderTemplate('form_welcome', array('header' => $header, 'text' => $text));

$command = '';
$countcommand = '';
if ($http->get('console')) {
	switch ($http->get('console')) {
		case '3ds':
			$console = '3ds';
			$command .= " AND t.downloadtheme3ds <> '' ";
			$countcommand .= " AND downloadtheme3ds <> '' ";
			break;
		case 'wiiu':
			$console = 'wiiu';
			$command .= " AND (t.downloadthemewiiu <> '' OR t.downloadcostumewiiu <> '') ";
			$countcommand .= " AND (downloadthemewiiu <> '' OR downloadcostumewiiu <> '') ";
			break;
		default:
			$console = '';
			$command = '';
			$countcommand = '';
	}
} else {
	$console = '';
	$command = '';
	$countcommand = '';
}

if ($http->get('style')) {
	switch ($http->get('style')) {
		case 'smb1':
			$style = 'smb1';
			$command .= " AND t.style = 'smb1' ";
			$countcommand .= " AND style = 'smb1' ";
			break;
		case 'smb3':
			$style = 'smb3';
			$command .= " AND t.style = 'smb3' ";
			$countcommand .= " AND style = 'smb3' ";
			break;
		case 'smw':
			$style = 'smw';
			$command .= " AND t.style = 'smw' ";
			$countcommand .= " AND style = 'smw' ";
			break;
		case 'nsmbu':
			$style = 'nsmbu';
			$command .= " AND t.style = 'nsmbu' ";
			$countcommand .= " AND style = 'nsmbu' ";
			break;
		case 'custom':
			$style = 'custom';
			$command .= " AND t.style = 'custom' ";
			$countcommand .= " AND style = 'custom' ";
			break;
		default:
			$style = '';
			$command .= '';
			$countcommand .= '';
	}
} else {
	$style = '';
	$command .= '';
	$countcommand .= '';
}

if ($http->get('theme')) {
	switch ($http->get('theme')) {
		case 'grass':
			$smmtheme = 'grass';
			$command .= " AND t.theme = 'grass' ";
			$countcommand .= " AND theme = 'grass' ";
			break;
		case 'under':
			$smmtheme = 'under';
			$command .= " AND t.theme = 'under' ";
			$countcommand .= " AND theme = 'under' ";
			break;
		case 'water':
			$smmtheme = 'water';
			$command .= " AND t.theme = 'water' ";
			$countcommand .= " AND theme = 'water' ";
			break;
		case 'castle':
			$smmtheme = 'castle';
			$command .= " AND t.theme = 'castle' ";
			$countcommand .= " AND theme = 'water' ";
			break;
		case 'ghost':
			$smmtheme = 'ghost';
			$command .= " AND t.theme = 'ghost' ";
			$countcommand .= " AND theme = 'ghost' ";
			break;
		case 'airship':
			$smmtheme = 'airship';
			$command .= " AND t.theme = 'airship' ";
			$countcommand .= " AND theme = 'ghost' ";
			break;
		default:
			$smmtheme = '';
			$command .= '';
			$countcommand .= '';
	}
} else {
	$smmtheme = '';
	$command .= '';
	$countcommand .= '';
}

$sidebarshow = true;
$rFora = Query("select * from {forums} where id = {0} ", 3);
if(NumRows($rFora))
{
	if(!HasPermission('forum.viewforum', $forum['id']))
		return;
	else
		$forum = Fetch($rFora);
} else {
	Alert("Whoops. Seems like there were no results for the fields you selected. Why not try different fields?");
	return;
}

$showconsoles = true;
$depoturl = 'depot';

$numThemes = FetchResult("select count(*) from threads where forum = 3 ".$countcommand);

RenderTemplate('form_lvluserpanel', array('submission' => $submissions));
$fid = $forum['id'];

$total = $forum['numthreads'];

if(isset($_GET['depotpage']))
	$depotpage = (int)$_GET['depotpage'];
else
	$depotpage = 0;

$tpp = 12;

$depotpagelinks = 'console='.$console.'&style='.$style.'&theme='.$smmtheme.'&depotpage=';

$rThreads = Query("	SELECT 
						t.id, t.icon, t.title, t.closed, t.replies, t.lastpostid, t.screenshot, t.description, t.downloadthemewiiu, t.downloadcostumewiiu, t.downloadtheme3ds,
						p.id pid, p.date,
						pt.text,
						su.(_userfields),
						lu.(_userfields)
					FROM 
						{threads} t
						LEFT JOIN {posts} p ON p.thread=t.id AND p.id=t.firstpostid
						LEFT JOIN {posts_text} pt ON pt.pid=p.id AND pt.revision=p.currentrevision
						LEFT JOIN {users} su ON su.id=t.user
						LEFT JOIN {users} lu ON lu.id=t.lastposter
					WHERE t.forum={0} AND p.deleted=0 ".$command."
					ORDER BY p.date DESC LIMIT {1u}, {2u}", $fid, $depotpage, $tpp);

$numonpage = NumRows($rThreads);

$pagelinks = PageLinks(pageLink('depot', [], $depotpagelinks), $tpp, $depotpage, $numThemes);

RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'top'));

echo '<div style="max-width: 90%; display: flex; flex-flow: row wrap; justify-content: space-around;">';

while($thread = Fetch($rThreads))
{
	$pdata = array();

	$starter = getDataPrefix($thread, 'su_');
	$last = getDataPrefix($thread, 'lu_');

	$pdata['screenshots'] = $thread['screenshot'];

	if ((strpos($pdata['screenshots'], 'https://www.youtube.com/') !== false) || (strpos($pdata['screenshots'], 'https://youtu.be/') !== false))
		$pdata['screenshot'] = str_replace("/watch?v=","/embed/", '<iframe width="280" height="157" src="'.$pdata['screenshots'].'" frameborder="0" allowfullscreen></iframe>');
	elseif(!empty($pdata['screenshots']))
		$pdata['screenshot'] = parseBBCode('[imgs]'.$pdata['screenshots'].'[/imgs]');
	elseif(preg_match('~iframe.+src=(?:&quot;|[\'"])(?:https?)\:\/\/www\.(?:youtube|youtube\-nocookie)\.com\/embed\/(.*?)(?:&quot;|[\'"])~iu', $pdata['text']) === 1){
		$pdata['screenshots'] = '2';
		preg_match('(\[youtube\](.*?)\[\/youtube\])', $pdata['text'], $match);
		$pdata['screenshot'] = str_replace("/watch?v=","/embed/", '<iframe width="280" height="157" src="'.$match[1].'" frameborder="0" allowfullscreen></iframe>');
	}
	$pdata['description'] = $thread['description'];

	$tags = ParseThreadTags($thread['title']);

	$pdata['download'] = '';
	if($thread['downloadtheme3ds'] !== '')
		$pdata['download'] .= '<a href="'.$thread['downloadtheme3ds'].'">Download 3DS Theme</a>';
	if(($thread['downloadtheme3ds'] !== '') && ($thread['downloadthemewiiu'] !== ''))
		$pdata['download'] .= ' | ';
	if($thread['downloadthemewiiu'] !== '')
		$pdata['download'] .= '<a href="'.$thread['downloadthemewiiu'].'">Download WiiU Theme</a>';
	if($thread['downloadcostumewiiu'] !== '')
		$pdata['download'] .= '<a href="'.$thread['downloadcostumewiiu'].'">Download WiiU Costume</a>';

	$pdata['title'] = '<img src="'.$thread['icon'].'"><a href="'.pageLink("entry", array(
				'id' => $thread['id'],
				'name' => slugify($tags[0])
			)).'">'.$tags[0].'</a><br>'.$tags[1];

	$pdata['formattedDate'] = formatdate($thread['date']);
	$pdata['userlink'] = UserLink($starter);
	$pdata['text'] = CleanUpPost($thread['text'],$starter['name'], false, false);

	if (!$thread['replies'])
		$comments = 'No comments yet';
	else if ($thread['replies'] < 2)
		$comments = actionLinkTag('1 comment', 'depost', $thread['lastpostid']).' (by '.UserLink($last).')';
	else
		$comments = actionLinkTag($thread['replies'].' comments', 'depost', $thread['lastpostid']).' (last by '.UserLink($last).')';
	$pdata['comments'] = $comments;

	if ($thread['closed'])
		$newreply = __('Comments closed.');
	else if (!$loguserid)
		$newreply = actionLinkTag(__('Log in'), 'login').__(' to post a comment.');
	else if (HasPermission('forum.postthreads', $forum['id']))
		$newreply = actionLinkTag(__("Post a comment"), "newcomment", $thread['id']);
	$pdata['replylink'] = $newreply;

	RenderTemplate('postdepo', array('post' => $pdata));
}

echo '</div>';

RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'bottom'));
?>