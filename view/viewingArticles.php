<?php
function getArticle()
{
    ?>
    <div class="container" id="contentContainerArticle">  
        <style> 
            #button1:hover {background-color: white;}
        </style>    
        <?php

	     //Получаем номер страницы 
	     if (isset($_GET['article'])){
            $page = $_GET['article'];
        } else {
            $page = 1;
        }

        //Получаем запрос поиска (если он есть)
        if (isset($_GET['searchArticle'])){
            $search = $_GET['searchArticle'];
        }

        //Название журнала, статьи которого показываются 
        if (isset($_GET['Journal'])){
            $Journal=$_GET['Journal'];
        }      
	    global $wpdb;
	    $pages = parse_url($_GET['Journal']);
	    $titleJournal = $pages['path']; //Название журнала 
        
        $notesOnPage = 10;   //Количество записей на странице
        $from = ($page - 1) * $notesOnPage;

	    $dataJournal = $wpdb->get_results("SELECT `id` FROM `wp_journal` WHERE (`name` = '$titleJournal')", ARRAY_A); 
	    $id = isset($dataJournal[0]['id']) ? $dataJournal[0]['id'] : false;

        $count = $wpdb->get_results("SELECT COUNT(*) as count 
            FROM wp_article WHERE (`Journal_id` = '$id')", ARRAY_A); //Получение количества записей в бд

        $vals = $wpdb->get_results("SELECT `id`, `name`, `abstract`, `scopus_id` 
            FROM `wp_article` WHERE (`Journal_id` = '$id') LIMIT $from, $notesOnPage", ARRAY_A);	
        
	    $pagesCount = ceil($count[0]["count"] / $notesOnPage); //Определение необходимого количества страниц
	    ?>
        <div class="container">
            <h2><?php echo $titleJournal ?></h2>
            <button class="btn btn-primary" type="button">
                <a href="<?php echo $Journal ?>" style="color:white" id="get_baton_article"> 
                    Refresh     
                </a>           
            </button>
        </div>
	    <div class="container" style="text-align: justify;">
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
                                <p style="text-align: left">
            					<?php
	    							echo($value['name']);
	    						?>
                                </p>
          					</button>
        				</h2>
        				<div id="<?php echo $aria_controls; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $h2id; ?>" data-bs-parent="#accordionFlushExample">
          					<div class="accordion-body">
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
                                        WHERE `wp_article_id` = '$articleId'", ARRAY_A);
                                    foreach ($sectionTitles as $sectionValue){
                                        $sectionId = $sectionValue['id'];
                                        $sectionText = $wpdb->get_results("SELECT `text`
                                            FROM `wp_article_section_text`
                                            WHERE `article_title_id` = '$sectionId'", ARRAY_A);
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
            
	    <!--Пагинация-->
	    <nav aria-label="Page navigation example"> 
            <ul class="pagination justify-content-center">
            <?php
            //Вывод кнопки "вернуться на первую страницу", если не выбрана уже первая
            if ($page != 1) {
                $prev = $page - 1;
                ?>  
                <li class="page-item" style="list-style-type: none;">
                    <?php
                    echo "<a class=\"page-link\" href=\"?article=1&Journal=$Journal\" aria-label=\"Previous\">
                            <span aria-hidden=\"true\">&laquo;</span>
                        </a>";
                    ?>
                </li> 
                <?php
            } 
            //Вывод кнопок перехода по страницам 
            if ($pagesCount<=3){
                for ($i = 1; $i <= $pagesCount; $i++){  
                    if ($page == $i) {
                        ?>
                        <li class="page-item active" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li class="page-item" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    }                 
                }
            } else {
                switch ($page) {
                    case $pagesCount-1: for ($i = $page - 2; $i <= $page+1; $i++){  
                        if ($page == $i) {
                            ?>
                            <li class="page-item active" aria-current="page" style="list-style-type: none;">
                                <?php
                                    echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                                ?>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" aria-current="page" style="list-style-type: none;">
                                <?php
                                    echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                                ?>
                            </li>
                            <?php
                        }                 
                    }break;
                    case $pagesCount: for ($i = $page - 2; $i <= $page; $i++){  
                        if ($page == $i) {
                            ?>
                            <li class="page-item active" aria-current="page" style="list-style-type: none;">
                                <?php
                                    echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                                ?>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" aria-current="page" style="list-style-type: none;">
                                <?php
                                    echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                                ?>
                            </li>
                            <?php
                        }                 
                    }break;
                    case 1: for ($i = $page; $i <= $page + 2; $i++){  
                        if ($page == $i) {
                            ?>
                            <li class="page-item active" aria-current="page" style="list-style-type: none;">
                                <?php
                                    echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                                ?>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" aria-current="page" style="list-style-type: none;">
                                <?php
                                    echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                                ?>
                            </li>
                            <?php
                        }                 
                    }break;
                    case 2: for ($i = $page - 1; $i <= $page + 2; $i++){  
                        if ($page == $i) {
                            ?>
                            <li class="page-item active" aria-current="page" style="list-style-type: none;">
                                <?php
                                    echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                                ?>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" aria-current="page" style="list-style-type: none;">
                                <?php
                                    echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                                ?>
                            </li>
                            <?php
                        }                 
                    }break;            
                    default: for ($i = $page - 2; $i <= $page + 2; $i++){  
                        if ($page == $i) {
                            ?>
                            <li class="page-item active" aria-current="page" style="list-style-type: none;">
                                <?php
                                    echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                                ?>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" aria-current="page" style="list-style-type: none;">
                                <?php
                                    echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                                ?>
                            </li>
                            <?php
                        }
                    }break; 
                }
            }

            //Вывод "кнопки перейти на последнюю страницу", если не выбрана последняя 
            if ($page != $pagesCount) {
                $prev = $page + 1;
                ?>  
                <li class="page-item" style="list-style-type: none;">
                    <?php
                        echo "<a class=\"page-link\" href=\"?article=$pagesCount&Journal=$Journal\" aria-label=\"Previous\">
                        <span aria-hidden=\"true\">&raquo;</span>
                        </a>";
                    ?>
                </li> 
                <?php
            }?>
            </ul>
        </nav>
    </div>
	<?php
}

function getArticleAlter($titleJournal)
{
    ?>
    <style> 
        #button1:hover {background-color: white;}
    </style>    
    <?php
    $page = 1;
    global $wpdb;
    $notesOnPage = 10;   //Количество записей на странице
    $from = ($page - 1) * $notesOnPage;
	$dataJournal = $wpdb->get_results("SELECT `id` FROM `wp_journal` WHERE (`name` = '$titleJournal')", ARRAY_A); 
	$id = $dataJournal[0]['id'];
    $count = $wpdb->get_results("SELECT COUNT(*) as count 
        FROM wp_article WHERE (`journal_id` = '$id')", ARRAY_A); //Получение количества записей в бд
    $vals = $wpdb->get_results("SELECT `id`, `name`, `abstract`, `scopus_id` 
        FROM `wp_article` WHERE (`journal_id` = '$id') LIMIT $from, $notesOnPage", ARRAY_A);
	$pagesCount = ceil($count[0]["count"] / $notesOnPage); //Определение необходимого количества страниц
	?>
    <div class="container">
        <h2><?php echo $titleJournal ?></h2>
        <button class="btn btn-primary" type="button">
            <a href="<?php echo $Journal ?>" style="color:white" id="get_baton_article"> 
                Refresh     
            </a>           
        </button>
    </div>
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
								echo($value['name']);
							?>
                            </p>
      					</button>
    				</h2>
    				<div id="<?php echo $aria_controls; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $h2id; ?>" data-bs-parent="#accordionFlushExample">
      					<div class="accordion-body">
						  	<h3>Abstract</h3>
							<?php
								echo($value['abstract']);
							?>
                            <p>
							<h3>Article</h3>
							<?php
								//echo($value['full_text']);
                                $articleId = $value['id'];
                                $sectionTitles = $wpdb->get_results("SELECT `id`, `name` 
                                    FROM `wp_article_section_title` 
                                    WHERE (`wp_article_id` = '$articleId') 
                                    LIMIT $from, $notesOnPage", ARRAY_A);
                                foreach ($sectionTitles as $sectionValue){
                                    $sectionId = $sectionValue['id'];
                                    $sectionText = $wpdb->get_results("SELECT `text`
                                        FROM `wp_article_section_text`
                                        WHERE (`article_title_id` = '$sectionId')
                                        LIMIT $from, $notesOnPage", ARRAY_A);
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
    <!--Пагинация-->
	<nav aria-label="Page navigation example"> 
        <ul class="pagination justify-content-center">
        <?php
        //Вывод кнопки "вернуться на первую страницу", если не выбрана уже первая
        if ($page != 1) {
            $prev = $page - 1;
            ?>  
            <li class="page-item" style="list-style-type: none;">
                <?php
                echo "<a class=\"page-link\" href=\"?article=1&Journal=$Journal\" aria-label=\"Previous\">
                        <span aria-hidden=\"true\">&laquo;</span>
                    </a>";
                ?>
            </li> 
            <?php
        } 
        //Вывод кнопок перехода по страницам 
        if ($pagesCount<=3){
            for ($i = 1; $i <= $pagesCount; $i++){  
                if ($page == $i) {
                    ?>
                    <li class="page-item active" aria-current="page" style="list-style-type: none;">
                        <?php
                            echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                        ?>
                    </li>
                    <?php
                } else {
                    ?>
                    <li class="page-item" aria-current="page" style="list-style-type: none;">
                        <?php
                            echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                        ?>
                    </li>
                    <?php
                }                 
            }
        } else {
            switch ($page) {
                case $pagesCount-1: for ($i = $page - 2; $i <= $page+1; $i++){  
                    if ($page == $i) {
                        ?>
                        <li class="page-item active" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li class="page-item" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    }                 
                }break;
                case $pagesCount: for ($i = $page - 2; $i <= $page; $i++){  
                    if ($page == $i) {
                        ?>
                        <li class="page-item active" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li class="page-item" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    }                 
                }break;
                case 1: for ($i = $page; $i <= $page + 2; $i++){  
                    if ($page == $i) {
                        ?>
                        <li class="page-item active" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li class="page-item" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    }                 
                }break;
                case 2: for ($i = $page - 1; $i <= $page + 2; $i++){  
                    if ($page == $i) {
                        ?>
                        <li class="page-item active" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li class="page-item" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    }                 
                }break;            
                default: for ($i = $page - 2; $i <= $page + 2; $i++){  
                    if ($page == $i) {
                        ?>
                        <li class="page-item active" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li class="page-item" aria-current="page" style="list-style-type: none;">
                            <?php
                                echo "<a class=\"page-link\" href=\"?article=$i&Journal=$Journal\">$i</a>";
                            ?>
                        </li>
                        <?php
                    }
                }break; 
            }
        }
        //Вывод "кнопки перейти на последнюю страницу", если не выбрана последняя 
        if ($page != $pagesCount) {
            $prev = $page + 1;
            ?>  
            <li class="page-item" style="list-style-type: none;">
                <?php
                    echo "<a class=\"page-link\" href=\"?article=$pagesCount&Journal=$Journal\" aria-label=\"Previous\">
                    <span aria-hidden=\"true\">&raquo;</span>
                    </a>";
                ?>
            </li> 
            <?php
        }?>
        </ul>
    </nav>
	<?php
}

add_shortcode('tshArticle', 'getArticle');
?>
