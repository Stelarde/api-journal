<?php
function getScientificSearchPage()
{
    global $wpdb;
    $user_id = get_current_user_id();
    ?>
    <div>
    <style>
        #button1:hover {
            background-color: white;
            margin: 0;
        }

        #button1 {
            margin: 0;
        }

        #headingOne {
            padding: 0;
        }

        #headingTwo {
            padding: 0;
        }

        #headingThree {
            padding: 0;
        }

        #batonFind {
            margin-top: 10px;
        }

        #batonFind a {
            color: white
        }

        ;
    </style>
    <?php
    $user_settings = $wpdb->get_results("SELECT `number_of_articles`, `from_year`, `to_year`, `keywords`
        FROM `wp_settings_load_article` WHERE (`user_id` = '$user_id')", ARRAY_A);
    ?>
    <div class='container' id='contentContainerSearch'>
        <form name="form" action="" method="get">
            <div class="container">
                <div class="col-md" style="margin: 0 0 16px;">
                    <label> Select all the journals of interest from which the articles will be used to create the sample search and which will be searched.</label>
                    <label> If you do not have an API key, then follow the <a href="https://dev.elsevier.com/" target="_blank">links</a> and click "I wont API Key". </label>
                    <label> If the API key is not entered, then the standard one is used, which is already built into the system. </label>
                </div>
                <div class="col-md">
                    <div class="form-floating">
                        <select class="form-select" id="inputGroupSelect">
                            <?php
                            if ($user_settings[0]['number_of_articles']){
                                switch($user_settings[0]['number_of_articles']) {
                                    case '10':
                                        ?>
                                        <option selected value="1">10</option>
                                        <option value="2">15</option>
                                        <option value="3">25</option>
                                        <?php
                                        break;
                                    case '15':
                                        ?>
                                        <option value="1">10</option>
                                        <option selected value="2">15</option>
                                        <option value="3">25</option>
                                        <?php
                                        break;
                                    case '25':
                                        ?>
                                        <option value="1">10</option>
                                        <option value="2">15</option>
                                        <option selected value="3">25</option>
                                        <?php
                                        break;
                                    }
                            } else {
                                ?>
                                <option value="1">10</option>
                                <option value="2">15</option>
                                <option value="3">25</option>
                                <?php
                            }
                            ?>
                        </select>
                        <label for="inputGroupSelect">Number of articles in the journal</label>
                    </div>
                </div>
                <div class="row g-2" style="margin: 16px 0 16px;">
                    <div class="col-md" style="padding: 0; margin: 0 5px 0 0;">
                        <div class="form-floating">
                            <?php
                                if ($user_settings[0]['from_year']){
                                    ?>
                                    <input value="<?php echo $user_settings[0]['from_year'] ?>" type="number" class="form-control" min="2004" max="2021" id="inpFromYear" style="border: 1px solid #dfdfdf; border-radius: 4px; width: 100%; height: 100%">
                                    <?php
                                } else {
                                    ?>
                                    <input type="number" class="form-control" min="2004" max="2021" id="inpFromYear" style="border: 1px solid #dfdfdf; border-radius: 4px; width: 100%; height: 100%">
                                    <?php
                                }
                            ?>
                            <label for="inpFromYear">Enter the year of publication start</label>
                        </div>
                    </div>
                    <div class="col-md" style="padding: 0; margin: 0 0 0 5px;">
                        <div class="form-floating">
                            <?php
                            if ($user_settings[0]['to_year']){
                                ?>
                                <input value="<?php echo $user_settings[0]['to_year'] ?>" type="number" class="form-control" min="2004" max="2021" id="inpToYear" style="border: 1px solid #dfdfdf; border-radius: 4px; width: 100%; height: 100%">
                                <?php
                            } else {
                                ?>
                                <input type="number" class="form-control" min="2004" max="2021" id="inpToYear" style="border: 1px solid #dfdfdf; border-radius: 4px; width: 100%; height: 100%">
                                <?php
                            }
                            ?>
                            <label for="inpToYear">Enter the year of the end of the publication</label>
                        </div>
                    </div>
                </div>
                <div class="col-md" style="margin: 0 0 16px;">
                    <div class="form-floating">
                        <?php
                        if ($user_settings[0]['keywords']){
                            ?>
                            <input value="<?php echo $user_settings[0]['keywords'] ?>" type="search" class="form-control" id="inputKeywords" style="margin-bottom: 0;">
                            <?php
                        } else {
                            ?>
                            <input type="search" class="form-control" id="inputKeywords" style="margin-bottom: 0;">
                            <?php
                        }
                        ?>
                        <label for="inputKeywords">Enter keywords from article</label>
                    </div>
                </div>
                <div class="col-md" style="margin: 0 0 16px;">
                    <div class="form-floating">
                        <input type="search" class="form-control" id="inputAPI" style="margin-bottom: 0;">
                        <label for="inputKeywords">Enter API-key</label>
                    </div>
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary" type="submit" id="batonGetArticle" style="margin-bottom: 0;">
                        Next
                    </button>
                </div>
                <div class="input-group mb-3">
                    <input type="search" class="form-control" id="searchJournalForEx" placeholder="Enter the title of the journal">
                    <button class="btn btn-primary" type="submit" id="getBatonSearchJournalForEx" style="margin-bottom: 0;">
                        Search
                    </button>
                </div>                
            </div>
        </form>

        <?php
        $journals = $wpdb->get_results("SELECT `id`, `name`, `url` 
                FROM `wp_journal`", ARRAY_A);
        $selectJournals = $wpdb->get_results("SELECT `name`, `journal_id`, `user_id`
                FROM `wp_select_journal_for_ex`", ARRAY_A);
        ?>
        <div id="contentContainerJournalsForEx">
            <?php
            foreach ($journals as $key => $journal) {
            ?>
                <div class="form-check">
                    <label class="form-check-label" for="flexCheckDefault" style="margin-right: 1em">
                        <?php
                        $fl = false;
                        foreach ($selectJournals as $selectJournal) {
                            if ($selectJournal['journal_id'] == $journal['id'] and $selectJournal['user_id'] == $user_id) {
                                $fl = true;
                                break;
                            }
                        }
                        if ($fl) {
                        ?>
                            <input class="checkJournalForEx" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault" checked>
                        <?php
                        } else {
                        ?>
                            <input class="checkJournalForEx" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault">
                        <?php
                        }
                        ?>
                        <?php echo $journal['name'] ?>
                    </label>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    </div>
<?php
}
add_shortcode('scientificsearchid1', 'getScientificSearchPage');

function getScientificSearchPageForEx($journals){
    global $wpdb;
    $user_id = get_current_user_id();
    $selectJournals = $wpdb->get_results("SELECT `name`, `journal_id`, `user_id`
            FROM `wp_select_journal_for_ex`", ARRAY_A);
    foreach ($journals as $key => $journal) {
    ?>
        <div class="form-check">
            <label class="form-check-label" for="flexCheckDefault" style="margin-right: 1em">
                <?php
                $fl = false;
                foreach ($selectJournals as $selectJournal) {
                    if ($selectJournal['journal_id'] == $journal['id'] and $selectJournal['user_id'] == $user_id) {
                        $fl = true;
                        break;
                    }
                }
                if ($fl) {
                ?>
                    <input class="checkJournalForEx" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault" checked>
                <?php
                } else {
                ?>
                    <input class="checkJournalForEx" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault">
                <?php
                }
                ?>
                <?php echo $journal['name'] ?>
            </label>
        </div>
    <?php
    }
}

function getScientificSearchPageGetArticles()
{
    global $wpdb;
    ?>
    <style>
        #button1:hover {
            background-color: white;
            margin: 0;
        }

        #button1 {
            margin: 0;
        }

        #headingOne {
            padding: 0;
        }

        #headingTwo {
            padding: 0;
        }

        #headingThree {
            padding: 0;
        }

        #batonNext {
            margin-top: 10px;
        }

        #batonNext a {
            color: white
        }

        ;
    </style>
    <div class="col-md" style="margin: 0 0 16px;">
        <label> Select which articles will be used to create a sample search. Alternatively, you can enter your own text to create the example.</label>
    </div>
    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" id="button1" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    Journals
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <form name="form" action="" method="get">
                        <div class="container">
                            <div class="mb-3">
                                <input type="search" class="form-control" id="searchJournalorArticle" placeholder="Enter the title of the journal, article or text from the abstract">
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary" type="submit" id="getBatonSearchJournal">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="contentContainerSearchJournal">
                        <div class="accordion" id="accordionExample">
                            <?php
                            $user_id = get_current_user_id();  
                            $selectJournals = $wpdb->get_results("SELECT `id`, `name`, `journal_id`, `user_id`
                                FROM `wp_select_journal_for_ex`
                                WHERE `user_id` = $user_id", ARRAY_A);
                            $selectJournalsSample = $wpdb->get_results("SELECT `journal_id`, `user_id`
                                FROM `wp_select_journal_for_sample`", ARRAY_A);
                            foreach ($selectJournals as $key => $journal) {
                                $h2id = "flush-heading" . $key;
                                $data_bs_target = "#flush-collapse" . $key;
                                $aria_controls = "flush-collapse" . $key;
                                $checkJournal = $journal['name'];
                            ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="<?php echo $h2id; ?>" style="padding-bottom: 0;">
                                        <button class="accordion-button collapsed" type="button" id="button1" data-bs-toggle="collapse" data-bs-target="<?php echo $data_bs_target; ?>" aria-expanded="false" aria-controls="<?php echo $aria_controls; ?>">
                                            <label class="form-check-label" for="flexCheckDefault" style="margin-right: 1em">
                                                <?php
                                                $fl = false;
                                                foreach ($selectJournalsSample as $selectJournalSample) {
                                                    if ($selectJournalSample['journal_id'] == $journal['id'] and $selectJournalSample['user_id'] == $user_id) {
                                                        $fl = true;
                                                        break;
                                                    }
                                                }
                                                if ($fl) {
                                                ?>
                                                    <input class="checkJournal" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault" checked>
                                                <?php
                                                } else {
                                                ?>
                                                    <input class="checkJournal" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault">
                                                <?php
                                                }
                                                ?>
                                            </label>
                                            <div class="journalName" id="<?php echo ($journal['name']); ?> ">
                                                <?php
                                                echo ($journal['name']);
                                                ?>
                                            </div>
                                        </button>
                                    </h2>
                                    <?php
                                    $id = $journal['journal_id'];
                                    $articles = $wpdb->get_results("SELECT `id`, `name`, `abstract`, `scopus_id` 
                                            FROM `wp_article` WHERE `Journal_id` = '$id' AND `user_id` = '$user_id'", ARRAY_A);
                                    $selectArticlesSample = $wpdb->get_results("SELECT `article_id`, `user_id`
                                            FROM `wp_article_select`", ARRAY_A);
                                    ?>
                                    <div id="<?php echo $aria_controls; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $h2id; ?>" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">
                                            <?php
                                            foreach ($articles as $articl) {
                                            ?>
                                                <div class="form-check">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        <?php
                                                        $fl = false;
                                                        foreach ($selectArticlesSample as $selectArticleSample) {
                                                            if ($selectArticleSample['article_id'] == $articl['id'] and $selectArticleSample['user_id'] == $user_id) {
                                                                $fl = true;
                                                                break;
                                                            }
                                                        }
                                                        if ($fl) {
                                                        ?>
                                                            <input class="checkArticle" type="checkbox" value="<?php echo $articl['name'] ?>" id="flexCheckDefault" checked>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input class="checkArticle" type="checkbox" value="<?php echo $articl['name'] ?>" id="flexCheckDefault">
                                                        <?php
                                                        }
                                                        ?>
                                                        <div style="border: 1px solid #dfdfdf; margin-bottom: 10px; padding: 7px; text-align: justify; border-radius: 5px;">
                                                            <h3> <?php echo $articl['name']; ?> </h3>
                                                            <p> <?php echo $articl['abstract']; ?> </p>
                                                        </div>
                                                    </label>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }                              
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" id="button1" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Your text
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Example textarea</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="5">
                        <?php
                        $userText = $wpdb->get_results("SELECT `text` 
                            FROM `wp_user_text_for_ex` WHERE (`user_id` = '$user_id')", ARRAY_A);
                        if (isset($userText[0]['text'])) {
                            echo ($userText[0]['text']);
                        }
                        ?>
                        </textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" id="button1" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Published articles
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <form name="form" action="" method="get">
                        <div class="container">
                            <div class="mb-3">
                                <input type="search" class="form-control" id="searchPublishedArticles" placeholder="Enter the title of the article">
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary" type="submit" id="getBatonSearchPublishedArticles">
                                    <a style="color:white">
                                        Search
                                    </a>
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php
                    $publishArticles = $wpdb->get_results("SELECT `id`, `post_title`, `guid` FROM `wp_posts` WHERE `post_type` = 'post' AND `post_status` = 'publish'", ARRAY_A);

                    $selectpublishArticles = $wpdb->get_results("SELECT `post_id`, `user_id`
                                            FROM `wp_select_post`", ARRAY_A);
                    ?>
                    <div id="contentContainerSearchPublishedArticles">
                        <?php
                        foreach ($publishArticles as $articl) {
                        ?>
                            <div class="form-check">
                                <label class="form-check-label" for="flexCheckDefault" id="chekLabelPublishArticle">
                                    <?php
                                    $fl = false;
                                    foreach ($selectpublishArticles as $selectpublishArticle) {
                                        if ($selectpublishArticle['post_id'] == $articl['id'] and $selectpublishArticle['user_id'] == $user_id) {
                                            $fl = true;
                                            break;
                                        }
                                    }
                                    if ($fl) {
                                    ?>
                                        <input class="checkPublishedArticle" type="checkbox" value="<?php echo $articl['post_title'] ?>" id="flexCheckDefault" checked>
                                    <?php
                                    } else {
                                    ?>
                                        <input class="checkPublishedArticle" type="checkbox" value="<?php echo $articl['post_title'] ?>" id="flexCheckDefault">
                                    <?php
                                    }
                                    ?>
                                    <a href="<?php echo $articl['guid']; ?>"><?php echo $articl['post_title']; ?></a>
                                </label>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div align="right">
        <button class="btn btn-primary" type="submit" id="batonNext">
            <a href="/journal-selection">Next</a>
        </button>
    </div>
<?php
}

function itemSearchJournal($searchResultJournal, $searchResultArticle, $query)
{
    global $wpdb;
    $user_id = get_current_user_id();
    ?>
    <div id="contentContainerSearchJournal">
        <div class="accordion accordion" id="accordionExample">
            <?php
            $selectJournalsSample = $wpdb->get_results("SELECT `journal_id`, `user_id`
                FROM `wp_select_journal_for_sample`", ARRAY_A);
            foreach ($searchResultJournal as $key => $journal) {
                $h2id = "flush-heading" . $key;
                $data_bs_target = "#flush-collapse" . $key;
                $aria_controls = "flush-collapse" . $key;
            ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="<?php echo $h2id; ?>" style="padding-bottom: 0;">
                        <button class="accordion-button collapsed" type="button" id="button1" data-bs-toggle="collapse" data-bs-target="<?php echo $data_bs_target; ?>" aria-expanded="false" aria-controls="<?php echo $aria_controls; ?>">
                            <label class="form-check-label" for="flexCheckDefault" style="margin-right: 1em">
                                <?php
                                $fl = false;
                                foreach ($selectJournalsSample as $selectJournalSample) {
                                    if ($selectJournalSample['journal_id'] == $journal['id'] and $selectJournalSample['user_id'] == $user_id) {
                                        $fl = true;
                                        break;
                                    }
                                }
                                if ($fl) {
                                ?>
                                    <input class="checkJournal" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault" checked>
                                <?php
                                } else {
                                ?>
                                    <input class="checkJournal" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault">
                                <?php
                                }
                                ?>
                            </label>
                            <div class="journalName" id="<?php echo ($journal['name']); ?> ">
                                <?php
                                echo ($journal['name']);
                                ?>
                            </div>
                        </button>
                    </h2>
                    <?php
                    $articles = array();
                    foreach ($searchResultArticle as $key => $value) {
                        if ($value['Journal_id'] == $journal['id']) {
                            $articles[$key] = [
                                "id" => $value['id'],
                                "name" => $value['name'],
                                "abstract" => $value['abstract']
                            ];
                        }
                    }
                    ?>
                    <div id="<?php echo $aria_controls; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $h2id; ?>" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <?php
                            $selectArticlesSample = $wpdb->get_results("SELECT `article_id`, `user_id`
                                FROM `wp_article_select`", ARRAY_A);
                            foreach ($articles as $articl) {
                            ?>
                                <div class="form-check">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        <?php
                                        $fl = false;
                                        foreach ($selectArticlesSample as $selectArticleSample) {
                                            if ($selectArticleSample['article_id'] == $articl['id'] and $selectArticleSample['user_id'] == $user_id) {
                                                $fl = true;
                                                break;
                                            }
                                        }
                                        if ($fl) {
                                        ?>
                                            <input class="checkArticle" type="checkbox" value="<?php echo $articl['name'] ?>" id="flexCheckDefault" checked>
                                        <?php
                                        } else {
                                        ?>
                                            <input class="checkArticle" type="checkbox" value="<?php echo $articl['name'] ?>" id="flexCheckDefault">
                                        <?php
                                        }
                                        ?>
                                        <div style="border: 1px solid #dfdfdf; margin-bottom: 10px; padding: 7px; text-align: justify; border-radius: 5px;">
                                            <h3> <?php echo $articl['name']; ?> </h3>
                                            <p> <?php echo $articl['abstract']; ?> </p>
                                        </div>
                                    </label>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
<?php
}

function itemSearchArticle($searchResultPublishedArticle, $query)
{
?>
    <div id="contentContainerSearchPublishedArticles">
        <?php
        $selectpublishArticles = $wpdb->get_results("SELECT `post_id`, `user_id`
            FROM `wp_select_post`", ARRAY_A);
        foreach ($searchResultPublishedArticle as $articl) {
        ?>
            <div class="form-check">
                <label class="form-check-label" for="flexCheckDefault" id="chekLabelPublishArticle">
                    <?php
                    $fl = false;
                    foreach ($selectpublishArticles as $selectpublishArticle) {
                        if ($selectpublishArticle['post_id'] == $articl['id'] and $selectpublishArticle['user_id'] == $user_id) {
                            $fl = true;
                            break;
                        }
                    }
                    if ($fl) {
                    ?>
                        <input class="checkPublishedArticle" type="checkbox" value="<?php echo $articl['post_title'] ?>" id="flexCheckDefault" checked>
                    <?php
                    } else {
                    ?>
                        <input class="checkPublishedArticle" type="checkbox" value="<?php echo $articl['post_title'] ?>" id="flexCheckDefault">
                    <?php
                    }
                    ?>
                    <a href="<?php echo $articl['guid']; ?>"><?php echo $articl['post_title']; ?></a>
                </label>
            </div>
        <?php
        }
        ?>
    </div>
<?php
}
