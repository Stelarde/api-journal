<?php
    function getJournalSelectionPage(){
        global $wpdb;
        $user_id = get_current_user_id();
        ?>
        <style> 
            #button1:hover {background-color: white; margin:0;}
            #button1 {margin: 0;}
            #headingOne {padding: 0;}
            #headingTwo {padding: 0;}
            #headingThree {padding: 0;}
            #batonFind {margin-top: 10px;}
            #batonFind a {color: white};
        </style> 
        <div class='container' id='contentContainerSelectJournal'>
            <form name="form" action="" method="get">
                <div class="container">
                    <div class="col-md" style="margin: 0 0 16px;">
                        <label>From the list of downloaded journals, select those journals in which to search for articles.</label>
                    </div>
                    <div class="mb-3">                
                        <input type="search" 
                            class="form-control" 
                            id="searchJournalAlt" 
                            placeholder="Enter the title of the journal"                                             
                        >
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary" type="submit" id="getBatonSearchJournalAlt">
                            <a style="color:white">
                                Search
                            </a>
                         </button>
                        <button class="btn btn-primary" type="submit" id="batonFind" style="margin-top: 0">
                            <a href="/found-articles">
                                Find similar 
                            </a>
                        </button>
                    </div>
                </div>
            </form>

            <?php
            $journals = $wpdb->get_results("SELECT wp_select_journal_for_ex.id, wp_select_journal_for_ex.name, wp_select_journal_for_ex.journal_id
                FROM `wp_select_journal_for_ex`
                INNER JOIN `wp_article`
                ON wp_select_journal_for_ex.journal_id = wp_article.journal_id
                WHERE wp_select_journal_for_ex.user_id = '$user_id'", ARRAY_A);
            $selectJournals = $wpdb->get_results("SELECT `name`, `journal_id`, `user_id`
                FROM `wp_select_journal`
                WHERE `user_id` = '$user_id'", ARRAY_A);   
            ?>
            <div id="contentContainerJournals">
            <?php
            foreach($journals as $key => $journal){
                if (!empty($journal) AND $journals[$key-1] != $journal){
                    ?>
                    <div class="form-check">
                        <label class="form-check-label" for="flexCheckDefault" style="margin-right: 1em">
                            <?php
                            $fl = false;
                            foreach ($selectJournals as $selectJournal){
                                if ($selectJournal['journal_id'] == $journal['journal_id'] and $selectJournal['user_id'] == $user_id){
                                    $fl = true;
                                    break;
                                }
                            }
                            if ($fl){
                                ?>
                                <input class="checkJournal" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault" checked> 
                                <?php
                            } else{
                                ?>
                                <input class="checkJournal" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault">
                                <?php
                            }                           
                            ?> 
                            <?php echo $journal['name'] ?>  
                        </label>
                    </div>
                    <?php 
                }                           
            }
            ?>
            </div>           
        </div>
        <?php
    }
    add_shortcode('journalSelectionid1', 'getJournalSelectionPage');

    function itemSearchJournalAlt($searchResultJournal){
        ?>
        <div id="contentContainerJournals">
            <?php
            global $wpdb;
            $user_id = get_current_user_id();
            $selectJournals = $wpdb->get_results("SELECT `name`, `journal_id`, `user_id`
                FROM `wp_select_journal`", ARRAY_A);
            foreach($searchResultJournal as $key => $journal){
                $name = $journal['name'];
                ?>
                <div class="form-check">
                <label class="form-check-label" for="flexCheckDefault" style="margin-right: 1em">
                <?php
                $fl = false;
                foreach ($selectJournals as $selectJournal){
                    if ($selectJournal['journal_id'] == $journal['id'] and $selectJournal['user_id'] == $user_id){
                        $fl = true;
                        break;
                    }
                }   
                if ($fl){
                    ?>
                    <input class="checkJournalForEx" type="checkbox" value="<?php echo $journal['name'] ?>" id="flexCheckDefault" checked> 
                    <?php
                } else{
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
        <?php
    }
