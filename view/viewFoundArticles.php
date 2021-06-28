<?php
    function getResultFound(){
        global $wpdb;   
        $user_id = get_current_user_id();
        ini_set('memory_limit', '512M');
        ?>
        <div id="contentContainerFoundArticle">
        <style> 
            #button1:hover {background-color: white;}
            .modal {
            display:    none;
            position:   fixed;
            z-index:    1000;
            top:        0;
            left:       0;
            height:     100%;
            width:      100%;
            background: rgba( 255, 255, 255, .8 ) 
                        url('http://flottenab.com/wp-content/uploads/2021/04/giphy.gif') 
                        50% 50% 
                        no-repeat;
            }
            /* When the body has the loading class, we turn
               the scrollbar off with overflow:hidden */
            #contentContainer.loading .modal {
                overflow: hidden;   
            }
            /* Anytime the body has the loading class, our
               modal element will be visible */
            #contentContainer.loading .modal {
                display: block;
            }
        </style>
                    
            <form name="form" action="" method="get">
                <div class="container">
                    <div class="mb-3">                
                        <input type="search" 
                            class="form-control" 
                            name="searchJournal" 
                            id="searchJournal" 
                            placeholder="Journal title" 
                            value="<?php if (isset($_GET['searchJournal'])) echo $_GET['searchJournal']; ?>"
                        >
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary" type="submit">
                            <a style="color:white">
                                Search
                            </a>
                        </button>   
                        <button class="btn btn-primary" type="submit" id="buttonGetFind">
                            Result
                        </button>                       
                    </div>
                </div>
            </form>
            <?php
            $vals = $wpdb->get_results("SELECT wp_article.id, wp_article.name, wp_article.abstract, wp_article.scopus_id, wp_result_analysis.result 
                FROM wp_result_analysis
                INNER JOIN wp_article
                ON wp_result_analysis.article_id = wp_article.id
                WHERE wp_result_analysis.user_id = '$user_id'", ARRAY_A);
            if (isset($vals)){
                ?>
                <div class="container" >
	    	    <div class="accordion accordion-flush" id="accordionFlushExample">
	    		<?php             
	    		foreach ($vals as $key => $value){
	    			$h2id = "flush-heading" . $key;
	    			$data_bs_target = "#flush-collapse" . $key;
	    			$aria_controls = "flush-collapse" . $key;
	    			?>
	    			<div class="accordion-item">
        				<h2 class="accordion-header" id="<?php echo $h2id; ?>">
          					<button class="accordion-button collapsed" type="button"  id="button1" data-bs-toggle="collapse" data-bs-target="<?php echo $data_bs_target; ?>" aria-expanded="false" aria-controls="<?php echo $aria_controls; ?>">
                                <p style="text-align: left;">
            					<?php     
                                    $name = trim($value['name'], " \t");                                   
	    							echo($name);
                                    echo(" - ");
                                    echo($value['result']);
	    						?>
                                </p>
          					</button>
        				</h2>
        				<div id="<?php echo $aria_controls; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $h2id; ?>" data-bs-parent="#accordionFlushExample">
          					<div class="accordion-body" style="text-align: justify">
                                <?php echo($value['id']); ?>
                                    <a href="http://flottenab.com/found-articles/?article=<?php echo $value["id"] ?>" class="btn btn-primary">
                                        Download PDF
                                    </a>
	    					  	<h3>Abstract</h3>
	    						<?php
	    							echo($value['abstract']);
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
            ?>
            <div class="modal"><!-- Place at bottom of page --></div> 
        </div>
        <?php
    }

    add_shortcode('FindResult', 'getResultFound');
    
    function getResult(){ 
        global $wpdb;   
        $user_id = get_current_user_id();
        $vals = $wpdb->get_results("SELECT wp_article.id, wp_article.name, wp_article.abstract, wp_article.scopus_id, wp_result_analysis.result 
            FROM wp_result_analysis
            INNER JOIN wp_article
            ON wp_result_analysis.article_id = wp_article.id
            WHERE wp_result_analysis.user_id = '$user_id'", ARRAY_A);
        ?>
        <form name="form" action="" method="get">
            <div class="container">
                <div class="mb-3">                
                    <input type="search" 
                        class="form-control" 
                        name="searchJournal" 
                        id="searchJournal" 
                        placeholder="Journal title" 
                        value="<?php if (isset($_GET['searchJournal'])) echo $_GET['searchJournal']; ?>"
                    >
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary" type="submit">
                        <a style="color:white">
                            Search
                        </a>
                    </button>   
                    <button class="btn btn-primary" type="submit" id="buttonGetFind">
                        Result
                    </button>                           
                </div>
            </div>
        </form>
        <div class="container" >
	    	<div class="accordion accordion-flush" id="accordionFlushExample">
	    		<?php            
	    		foreach ($vals as $key => $value){
	    			$h2id = "flush-heading" . $key;
	    			$data_bs_target = "#flush-collapse" . $key;
	    			$aria_controls = "flush-collapse" . $key;
	    			?>
	    			<div class="accordion-item">
        				<h2 class="accordion-header" id="<?php echo $h2id; ?>">
          					<button class="accordion-button collapsed" type="button"  id="button1" data-bs-toggle="collapse" data-bs-target="<?php echo $data_bs_target; ?>" aria-expanded="false" aria-controls="<?php echo $aria_controls; ?>">
                                <p>
            					<?php
                                    echo($value['result']);
	    							echo($value['name']);
	    						?>
                                </p>
          					</button>
        				</h2>
        				<div id="<?php echo $aria_controls; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $h2id; ?>" data-bs-parent="#accordionFlushExample">
          					<div class="accordion-body" style="text-align: justify">
	    					  	<h3>Abstract</h3>
	    						<?php
	    							echo($value['abstract']);
	    						?>
                                <p>
	    						<h3>Article</h3>
	    						<?php
                                    $articleId = $value['id'];
                                    $sectionTitles = $wpdb->get_results("SELECT `id`, `name` 
                                        FROM `wp_article_section_title` 
                                        WHERE (`wp_article_id` = '$articleId')", ARRAY_A);
                                    foreach ($sectionTitles as $sectionValue){
                                        $sectionId = $sectionValue['id'];
                                        $sectionText = $wpdb->get_results("SELECT `text`
                                            FROM `wp_article_section_text`
                                            WHERE (`article_title_id` = '$sectionId')", ARRAY_A);
                                        ?>
                                        <h6><?php echo $sectionValue['name']; ?></h6>
                                        <p>
                                        <?php 
                                        if (isset($sectionText[0]['text'])){
                                            echo $sectionText[0]['text'];
                                        }                                   
                                        ?>
                                        </P>
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
        <div class="modal"><!-- Place at bottom of page --></div>
        <?php
    }

    add_action( 'init', 'getPdf' );
    function getPdf() {
        if (isset($_GET['article'])){
            require_once plugin_dir_path(__FILE__) . '../api/apiRequest.php';
            global $wpdb;

            $idArticle = $_GET['article'];

            $value = $wpdb->get_results("SELECT wp_article.scopus_id 
              FROM wp_article 
              WHERE wp_article.id = $idArticle", ARRAY_A);

            $scopusId = $value[0]['scopus_id'];

            $url = "https://api.elsevier.com/content/article/scopus_id/$scopusId ";
            $apiKey = 'e8d8b8c1bc7969b9827b72bc22a9541a';
            $accept = "application/pdf";
            $query = array(
              "view" => "FULL"
            );
        
            $data = apiRequestPDF($url, $apiKey, $query, $accept);

            header("Content-type: application/pdf; charset=utf-8");
            header("Content-Disposition: attachment; filename=ExportArticle.pdf");
            echo $data;
        }
    }
