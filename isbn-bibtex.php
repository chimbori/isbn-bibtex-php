<?php
  header('Access-Control-Allow-Origin: *');
  require_once('isbn-fetcher.php');

  if (isset($_POST['isbn'])) {
    // Clean up ISBN by removing all non-numeric characters from it.
    $isbn = preg_replace("/[^\\dXx]*/", "", $_POST['isbn']);
    $m = ISBNFetcher::getMetadata($isbn);
    if (isset($m->Error)) {
      if ($m->Error->Code === 503) {
        echo("@comment{API key disabled}");
      } else {
        http_response_code(404);
        echo(sprintf("@comment{\n".
            "  Error fetching metadata for ISBN %s. Check if it is valid by searching at Amazon.com.\n".
            "  %s : %s\n".
            "}",
            $isbn, $m->Error->Code, $m->Error->Message));
      }
      exit();
    }

    // For some unknown reason, converting the ItemAttributes->Author
    // property directly to an array yields only the first item.
    $authors = array();
    foreach ($m->ItemAttributes->Author as $author) {
      $authors[] = $author;
    }

    $bibAuthors = join(" and ", $authors);
    $bibtexTemplate = "@book{%s,\n  Author = {%s},\n  Title = {%s},\n  Publisher = {%s},\n  Year = {%s},\n  ISBN = {%s},\n  URL = {%s}\n}";

    $bibtex = sprintf($bibtexTemplate, $isbn,
        escapeSpecialChars($bibAuthors),
        escapeSpecialChars($m->ItemAttributes->Title),
        escapeSpecialChars($m->ItemAttributes->Publisher),
        substr($m->ItemAttributes->PublicationDate, 0, 4),
        $m->ItemAttributes->ISBN,
        $m->DetailPageURL);
    echo($bibtex);
  }

  function escapeSpecialChars($unsafe) {
    return str_replace("&", "\&", $unsafe);
  }
?>
