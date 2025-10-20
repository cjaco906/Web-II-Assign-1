<?php
require_once "include/header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Company</title>

    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/deflist.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php
    render_header();
    ?>
    <main>
        <article>
            <section class="about neat-shadow">
                <h1 class="about-title">ABOUT</h1>
                <div class="about-details nea">
                    <p>
                        My name is Ramos&mdash;A student in Mount Royal University.
                        <br>
                        I solely made this website to complete the first (#1) assignment for COMP 3512 (Web II).
                    </p>
                    <p>
                        The website illustrates an example of a stock portfolio management system,
                        <br>
                        which is built to practice and study for the following technologies:
                    </p>
                    <ul>
                        <li>
                            <hl>HTML5</hl>&mdash;Structuring the website
                            <ul>
                                <li>Studies integration of HTML with PHP.</li>
                                <li>Studies semantic tags and structuring.</li>
                            </ul>
                        </li>
                        <li>
                            <hl>CSS</hl>&mdash;Styling the website's appearance
                            <ul>
                                <li>Styles the web pages with consistent visuals.</li>
                                <li>Studies user experience.</li>
                            </ul>
                        </li>
                        <li>
                            <hl>PHP</hl>&mdash;Business logic processing
                            <ul>
                                <li>
                                    Performs query string paramater processing.
                                </li>
                                <li>
                                    Calls database for data retrieval via the PDO API.
                                </li>
                                <li>
                                    Creates a few classes for data encapsulation.
                                </li>
                            </ul>
                        </li>
                        <li>
                            <hl>SQLite</hl>&mdash;Data retrieval processing
                            <ul>
                                <li>
                                    Performs <code>SUM()</code>, <code>COUNT()</code>, <code>AVG()</code> aggregate functions.
                                </li>
                                <li>
                                    Performs selection, filtering, and grouping of rows and joins (i.e., <code>INNER JOIN</code>) to retrieve data.
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <p>
                        The following link below leads to my GitHub repository that contains the source code for this website.
                    </p>
                    <ul>
                        <li class="source-code-link"><a href="https://github.com/cjaco906/Web-II-Assign-1"><code>Web-II-Assign-1 (@cjaco906)</code></a></li>
                    </ul>
                </div>
            </section>
        </article>
    </main>
</body>
</html>
