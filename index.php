<html>
  <head>
    <meta charset="utf-8">
    <title>ISBN to BibTeX Lookup Tool</title>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400">
    <meta name="description" content="Lookup the BibTeX entry for a book using its ISBN">
    <style>
      * {
        box-sizing: border-box;
        font-family: 'Open Sans', Verdana, Helvetica, sans-serif;
      }
      body {
        margin: 0;
        font-size: 18px;
      }
      article {
        max-width: 1000px;
      }
      article {
        margin: 48px auto;
      }
      body, p, td, li {
        color: #333;
        line-height: 1.4rem;
        font-weight: normal;
        font-size: 14px;
      }
      header,
      footer {
        text-align: center;
        background: #333;
        padding: 24px;
        color: #eee;
      }
      header a:link,
      header a:visited,
      footer a:link,
      footer a:visited {
        color: #eee;
        text-decoration: none;
        border-bottom: 1px solid white;
      }
      h1, h2 {
        margin: 24px 0 0 0;
      }
      h1 {
        font-weight: 300;
        font-size: 42px;
        line-height: 84px;
      }
      h2 {
        font-weight: 300;
        font-size: 28px;
        line-height: 48px;
        margin-top: 48px;
      }
      input[type=text],
      textarea {
        font-size: 16px;
        width: 100%;
        border: 1px solid #ccc;
        padding: 12px;
        overflow-x: auto;
      }
      input[type=submit],
      .button {
        border-radius: 8px;
        padding: 12px;
        font-size: 16px;
        background-color: #44c767;
        border-radius: 28px;
        border: 1px solid #18ab29;
        display: inline-block;
        cursor: pointer;
        color: #ffffff;
        padding: 16px 64px;
        text-decoration: none;
        text-shadow: 0px 1px 35px #2f6627;
      }

      .highlighted-box {
        border: 2px solid red;
        border-radius: 12px;
        padding: 24px;
      }
      .highlighted-box h2 {
        margin: 0;
      }
    </style>
  </head>
  <body>
    <header>
      <h1>ISBN to BibTeX Lookup Tool</h1>
    </header>

    <article>
      <div class="action-box">
        <form id="bibtex-form" action="javascript:void(0);">
          <p>
            <label>
              <h3>ISBN: (e.g. 978-3639174304)</h3>
              <input type="text" name="isbn" value="" placeholder="e.g. 978-3639174304" id="isbn" size="25" maxlength="20" class="big wide">
            </label>
          </p>
          <p style="text-align: center"><input type="submit" class="button" value="Get BibTeX Citation"></p>
          <p><textarea placeholder="Enter ISBN above, then click the button to view citation here." id="bibtex-output" rows="12" cols="60" class="wide"></textarea></p>
        </form>
      </div>

      <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
      <script>
        $('#bibtex-form').submit(function() {
          $.ajax({
            type: 'POST',
            url: 'isbn-bibtex.php',
            data: {
              isbn: $('#isbn').val()
            },
            success: function(data) {
              $("#bibtex-output").text(data);
            },
            error: function(jqxhr) {
              $("#bibtex-output").text(jqxhr.responseText);
            }
          });
          return false;
        });
      </script>
    </article>
  </body>
</html>
