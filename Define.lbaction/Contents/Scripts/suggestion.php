<?

function findWords($search, $language = "en", $maxWords = 20)
{

	$search = strtolower(trim($search));

	if (strlen($search) == 0) return [];

	global $argv;

	$file = str_replace('Scripts/suggestion.php', "Resources/$language.lproj/dictionary.txt", $argv[0]);

	if (! file_exists($file))
	{
		$file = "/usr/share/dict/words";
	}

	exec("grep \"$search\" \"$file\"", $words);

	$scores = [];
	foreach ($words as $word)
	{
		$scores[$word] = levenshtein($search, $word);
	}

	usort($words, function($a, $b) use ($scores)
	{
		$aVal = $scores[$a];
		$bVal = $scores[$b];

		if ($aVal < $bVal) return -1;
		if ($aVal > $bVal) return 1;
		if ($aVal == $bVal) return 0;
	});

	$words = array_slice($words, 0, $maxWords);
	return $words;
}

$words = findWords( $argv[1] );

$suggestions = [];
foreach( $words as $word )
{
	$suggestions[] = [
		'title' => $word,
		'icon' => 'com.apple.Dictionary',
	];
}

echo json_encode($suggestions);

?>