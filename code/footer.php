<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


?>

</div>

    <div id="footer">
      <div class="container">
          <p class="text-muted pull-left">&copy;2014 <?=$info['Name']?></p>
          <p class="text-muted pull-right">Hosted by <a href="/">CharityHosting.ie</a></p>
      </div>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/jquery.tablesorter.min.js"></script>
    <script src="/js/bootstrap-colorpicker.js"></script>
    <script src="/js/jquery.tablesorter.widgets.min.js"></script>
    <script>
    $(function(){
            $('table').tablesorter({
                    widgets        : ['zebra', 'columns'],
                    usNumberFormat : false,
                    sortReset      : true,
                    sortRestart    : true
            });
    });
    </script>
  </body>
</html>

