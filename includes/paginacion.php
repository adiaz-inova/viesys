<?php
    function paginacion($query, $per_page = 30, $page = 1, $url = '?'){        
        $query = "SELECT COUNT(*) as `num` FROM ({$query})as tabla";
        $row = mysql_fetch_array(mysql_query($query));
        $total = $row['num'];
        $adjacents = "2"; 
 
        $page = ($page == 0 ? 1 : $page);  
        $start = ($page - 1) * $per_page;                               
         
        $prev = $page - 1;                          
        $next = $page + 1;
        $lastpage = ceil($total/$per_page);
        $lpm1 = $lastpage - 1;
         
        $pagination = "";
        if($lastpage > 1)
        {   
            $pagination .= "<div id='div_pagination_vie'><ul class='pagination_vie'>";
                    $pagination .= "<li class='details'>Página $page de $lastpage</li>";
            if ($lastpage < 7 + ($adjacents * 2))
            {   
                for ($counter = 1; $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>$counter</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}pag=$counter'>$counter</a></li>";                    
                }
            }
            elseif($lastpage > 5 + ($adjacents * 2))
            {
                if($page < 1 + ($adjacents * 2))     
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<li><a class='current'>$counter</a></li>";
                        else
                            $pagination.= "<li><a href='{$url}pag=$counter'>$counter</a></li>";                    
                    }
                    $pagination.= "<li class='dot'>...</li>";
                    $pagination.= "<li><a href='{$url}pag=$lpm1'>$lpm1</a></li>";
                    $pagination.= "<li><a href='{$url}pag=$lastpage'>$lastpage</a></li>";      
                }
                elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                {
                    $pagination.= "<li><a href='{$url}pag=1'>1</a></li>";
                    $pagination.= "<li><a href='{$url}pag=2'>2</a></li>";
                    $pagination.= "<li class='dot'>...</li>";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<li><a class='current'>$counter</a></li>";
                        else
                            $pagination.= "<li><a href='{$url}pag=$counter'>$counter</a></li>";                    
                    }
                    $pagination.= "<li class='dot'>..</li>";
                    $pagination.= "<li><a href='{$url}pag=$lpm1'>$lpm1</a></li>";
                    $pagination.= "<li><a href='{$url}pag=$lastpage'>$lastpage</a></li>";      
                }
                else
                {
                    $pagination.= "<li><a href='{$url}pag=1'>1</a></li>";
                    $pagination.= "<li><a href='{$url}pag=2'>2</a></li>";
                    $pagination.= "<li class='dot'>..</li>";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<li><a class='current'>$counter</a></li>";
                        else
                            $pagination.= "<li><a href='{$url}pag=$counter'>$counter</a></li>";                    
                    }
                }
            }
             
            if ($page < $counter - 1){ 
                $pagination.= "<li><a href='{$url}pag=$next'>Siguiente</a></li>";
                $pagination.= "<li><a href='{$url}pag=$lastpage'>Ultima</a></li>";
            }else{
                $pagination.= "<li><a class='current'>Siguiente</a></li>";
                $pagination.= "<li><a class='current'>Ultima</a></li>";
            }
            $pagination.= "</ul></div>\n";      
        }
     
     
        return $pagination;
    } 
?>