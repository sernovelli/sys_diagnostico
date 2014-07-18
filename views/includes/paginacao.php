<div id="controls">
	<div id="perpage">
		<select onchange="sorter.size(this.value)">
		<option value="5">5</option>
			<option value="10">10</option>
			<option value="20" selected="selected">20</option>
			<option value="50">50</option>
			<option value="100">100</option>
		</select>
		<span>Itens por p&aacute;gina</span>
	</div>
	<div id="navigation">
		<img src="views/includes/images/first.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)" />
		<img src="views/includes/images/previous.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1)" />
		<img src="views/includes/images/next.gif" width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
		<img src="views/includes/images/last.gif" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)" />
	</div>
	<div id="text">Exibindo p&aacute;gina <span id="currentpage">1</span> de <span id="pagelimit">2</span></div> 
</div>
   
<script type="text/javascript" src="views/includes/js/datatable/packed.js"></script>
<script type="text/javascript" src="views/includes/js/datatable/script.js"></script>
<script type="text/javascript">
      var sorter = new TINY.table.sorter("sorter");
	 sorter.head = "head"; //header class name
	 sorter.asc = "asc"; //ascending header class name
	 sorter.desc = "desc"; //descending header class name
	 sorter.even = "evenrow"; //even row class name
	 sorter.odd = "oddrow"; //odd row class name
	 sorter.evensel = "evenselected"; //selected column even class
	 sorter.oddsel = "oddselected"; //selected column odd class
	 sorter.paginate = true; //toggle for pagination logic
	 //sorter.pagesize = 15 (20); //toggle for pagination logic
	 sorter.currentid = "currentpage"; //current page id
	 sorter.limitid = "pagelimit"; //page limit id
	 sorter.init("table",1); // the id of the table and the initially sorted column index (optional)
</script>