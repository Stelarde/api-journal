<?php
function getJournal()
{
    ?>
    <div class="container" id="contentContainer">  
    <style>
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
    <?php
    #region Получаем номер страницы 
    if (isset($_GET['Journal'])){
        $page = $_GET['Journal'];
    } else {
        $page = 1;
    }
    $swPage = $page;
    #endregion

    #region Получаем запрос поиска (если он есть)
    if (isset($_GET['searchJournal'])){
        $search = $_GET['searchJournal'];
    }
    #endregion

    #region Поиск/фильтрация списка журналов
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
                <button class="btn btn-primary" type="button" id="get_baton">
                    Refresh 
                </button>
            </div>
        </div>
    </form>
    <?php
    #endregion

    #region Вывод списка журналов   
    $notesOnPage = 24;   //Количество записей на странице
    $from = ($page - 1) * $notesOnPage;

    global $wpdb;
    #endregion

    #region Если строка поиска пустая, то загрузить все записи из бд, либо с условием поиска
    if (empty($search)){  
        //Получение записей из бд, выводимых на страницу
        $results = $wpdb->get_results("SELECT `name`, `url` 
            FROM `wp_journal` LIMIT $from, $notesOnPage", ARRAY_A);   
        //Получение количества записей в бд
        $count = $wpdb->get_results("SELECT COUNT(*) as count 
            FROM wp_journal", ARRAY_A); 
    } else {
        //Получение записей из бд, выводимых на страницу
        $results = $wpdb->get_results("SELECT `name`, `url` FROM `wp_journal` 
            WHERE `name` LIKE '%$search%' LIMIT $from, $notesOnPage", ARRAY_A); 
        //Получение количества записей в бд          
        $count = $wpdb->get_results("SELECT COUNT(*) as count FROM `wp_journal` 
            WHERE `name` LIKE '%$search%'", ARRAY_A); 
    }   
    $pagesCount = ceil($count[0]["count"] / $notesOnPage); //Определение необходимого количества страниц
    $results = array_chunk($results, 3); //Разбивка массива записей в таблицу
    #endregion

    #region Показываем контент           
    foreach($results as $items): ?>
        <div class="row">
            <?php foreach ($items as $row): ?>
                <div class="col-lg my-2">
                    <div class="card" style="width: 13rem; height: 30rem">
                        <img src=<?php
                            echo $row["url"];                                                             
                            ?> 
                            onError="this.src='https://upload.wikimedia.org/wikipedia/ru/thumb/e/e7/Elsevier.svg/1200px-Elsevier.svg.png'"
                            class="card-img-top" alt=<?php echo $row["name"]?>>
                        <div class="card-body">
                          <p class="card-title"><?php echo $row["name"] ?></p>
                          <a href="http://flottenab.com/searcharticle/?Journal=<?php echo $row["name"] ?>" class="btn btn-primary">Articles</a>
                        </div>
                    </div>                        
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; 
    #endregion
        ?>    

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
                    if (empty($search)){
                        echo "<a class=\"page-link\" href=\"?Journal=1\" aria-label=\"Previous\">
                            <span aria-hidden=\"true\">&laquo;</span>
                        </a>";
                    } else {
                        echo "<a class=\"page-link\" href=\"?Journal=1&searchJournal=$search\" aria-label=\"Previous\">
                            <span aria-hidden=\"true\">&laquo;</span>
                        </a>";
                    }                
                    ?>
                </li> 
                <?php
            } 
            //Вывод кнопок перехода по страницам 
            if ($pagesCount <= 3){
                for ($i = 1; $i <= $pagesCount; $i++){  
                    if ($page == $i) {
                        ?>
                        <li class="page-item active" aria-current="page" style="list-style-type: none;">
                            <?php
                                if (empty($search)){
                                    echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                } else {
                                    echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                }                            
                            ?>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li class="page-item" aria-current="page" style="list-style-type: none;">
                            <?php
                                if (empty($search)){
                                    echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                } else {
                                    echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                }                            
                            ?>
                        </li>
                        <?php
                    }                 
                }
            } else {
                switch ($page) {
                    case 1: for ($i = $page; $i <= $page + 2; $i++){  
                        if ($page == $i) {
                            ?>
                            <li class="page-item active" aria-current="page" style="list-style-type: none;">
                                <?php
                                    if (empty($search)){
                                        echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                    } else {
                                        echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                    }                                
                                ?>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" aria-current="page" style="list-style-type: none;">
                                <?php
                                    if (empty($search)){
                                        echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                    } else {
                                        echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                    }                                
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
                                    if (empty($search)){
                                        echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                    } else {
                                        echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                    }                                
                                ?>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" aria-current="page" style="list-style-type: none;">
                                <?php
                                    if (empty($search)){
                                        echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                    } else {
                                        echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                    }                                
                                ?>
                            </li>
                            <?php
                        }                 
                    }break;   
                    case $pagesCount-1: for ($i = $page - 2; $i <= $page+1; $i++){  
                        if ($page == $i) {
                            ?>
                            <li class="page-item active" aria-current="page" style="list-style-type: none;">
                                <?php
                                    if (empty($search)){
                                        echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                    } else {
                                        echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                    }                                
                                ?>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" aria-current="page" style="list-style-type: none;">
                                <?php
                                    if (empty($search)){
                                        echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                    } else {
                                        echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                    }                                
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
                                    if (empty($search)){
                                        echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                    } else {
                                        echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                    }                                
                                ?>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" aria-current="page" style="list-style-type: none;">
                                <?php
                                    if (empty($search)){
                                        echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                    } else {
                                        echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                    }                                
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
                                    if (empty($search)){
                                        echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                    } else {
                                        echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                    }                                
                                ?>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="page-item" aria-current="page" style="list-style-type: none;">
                                <?php
                                    if (empty($search)){
                                        echo "<a class=\"page-link\" href=\"?Journal=$i\">$i</a>";
                                    } else {
                                        echo "<a class=\"page-link\" href=\"?Journal=$i&searchJournal=$search\">$i</a>";
                                    }                                
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
                        if (empty($search)){
                            echo "<a class=\"page-link\" href=\"?Journal=$pagesCount\" aria-label=\"Previous\">
                            <span aria-hidden=\"true\">&raquo;</span>
                            </a>";
                        } else {
                            echo "<a class=\"page-link\" href=\"?Journal=$pagesCount&searchJournal=$search\" aria-label=\"Previous\">
                            <span aria-hidden=\"true\">&raquo;</span>
                            </a>";
                        }

                    ?>
                </li> 
                <?php
            }?>
            </ul>
        </nav>   
        <div class="modal"><!-- Place at bottom of page --></div> 
    </div>
    <?php
} 

add_shortcode('tshJournal', 'getJournal');