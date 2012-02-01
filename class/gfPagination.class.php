<?php
/**
 * PHPSense Pagination Class
 *
 * PHP tutorials and scripts
 *
 * @package		PHPSense
 * @author		Jatinder Singh Thind
 * @copyright	Copyright (c) 2006, Jatinder Singh Thind
 * @link		http://www.phpsense.com
 */

// ------------------------------------------------------------------------
require_once 'gfCRUD.class.php';

class PS_Pagination {
	private $_php_self;
	private $_rows_per_page = 10; //Number of records to display per page
	private $_total_rows = 0; //Total number of rows returned by the query
	private $_links_per_page = 5; //Number of links to display per page
	private $_append = ""; //Paremeters to append to pagination links
	private $_sql = "";		
	private $_page = 1;
	private $_max_pages = 0;
	private $_offset = 0;
	private $_crud;//CRUD object 	
		
	/**
	 *
	 * @param type $crud		Object of CRUD Class
	 * @param type $sql		SQL Query to Paginate.
	 * @param type $rows_per_page	$rows_per_page Number of records to display per page. Defaults to 10
	 * @param type $links_per_page	$links_per_page Number of links to display per page. Defaults to 5
	 * @param type $append		Parameters to be appended to pagination links 
	 */
	public function __construct($crud, $sql, $rows_per_page = 10, $links_per_page = 5, $append = "") {
		$this->_crud = $crud;		
		$this->_sql = $sql;
		$this->_rows_per_page = (int)$rows_per_page;
		if (intval($links_per_page ) > 0) {
			$this->_links_per_page = (int)$links_per_page;
		} else {
			$this->_links_per_page = 5;
		}
		$this->_append = $append;
		$this->_php_self = htmlspecialchars($_SERVER['PHP_SELF'] );
		if (isset($_GET['page'] )) {
			$this->_page = intval($_GET['page'] );
		}
	}
	
	/**
	 * Executes the SQL query and initializes internal variables
	 *
	 * @access public
	 * @return resultSet after filtering records
	 */
	public function paginate() {
		
		//$this->_crud->conn();
		$stmt= $this->_crud->getDbConn()->prepare($this->_sql);
		$stmt->execute();

		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$this->_total_rows = count($result);
		//Return FALSE if no rows found
		if ($this->_total_rows == 0) {		    		    
		    return FALSE;
		}
		
		//Max number of pages
		$this->_max_pages = ceil($this->_total_rows / $this->_rows_per_page );
		if ($this->_links_per_page > $this->_max_pages) {
			$this->_links_per_page = $this->_max_pages;
		}
		fb($this->_page);
		//Check the page value just in case someone is trying to input an aribitrary value
		if ($this->_page > $this->_max_pages || $this->_page <= 0) {
			$this->_page = 1;
		}
		
		//Calculate Offset
		$this->_offset = $this->_rows_per_page * ($this->_page - 1);
		
		//Fetch the required result set
		$req_rs_sql = $this->_sql . " LIMIT {$this->_offset}, {$this->_rows_per_page}";
		$stmt = $this->_crud->getDbConn()->prepare($req_rs_sql);
		$stmt->execute();

		$reqResultSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if (! $reqResultSet) {
		    /*if (Debug::getDebug()){	
			fb(mysql_error(), 'Pagination query failed. Check your query.<br /><br />Error Returned:', FirePHP::ERROR);
			//Fb::warn("Pagination query failed. Check your query.<br /><br />Error Returned: " . mysql_error());
		    }*/			
		    return false;
		}				
		return $reqResultSet;
	}
	
	/**
	 * Display the link to the first page
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to 'First'
	 * @return string
	 */
	public function renderFirst($tag = 'First') {
		if ($this->_total_rows == 0)
			return FALSE;		
		if ($this->_page == 1) {
			return "<span class='page_link disabled'><a href='#'>$tag</a></span>";			
		} else {
		    return '<span class="page_link"><a href="' . $this->_php_self . '?page=1&row_pp='.$this->_rows_per_page.'&' . $this->_append . '">' . $tag . '</a></span>';		    
		}
	}
	
	/**
	 * Display the link to the last page
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to 'Last'
	 * @return string
	 */
	public function renderLast($tag = 'Last') {
		if ($this->_total_rows == 0)
			return FALSE;
		
		if ($this->_page == $this->_max_pages) {
			return "<span class='page_link disabled last'><a href='#'>$tag</a></span>";			
		} else {
			return '<span class="page_link last"><a href="' . $this->_php_self . '?page=' . $this->_max_pages . '&row_pp='.$this->_rows_per_page.'&' . $this->_append . '">' . $tag . '</a></span>';			
		}
	}
	
	/**
	 * Display the next link
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to '>>'
	 * @return string
	 */
	public function renderNext($tag = 'Next &rarr;') {
		if ($this->_total_rows == 0)
			return FALSE;
		
		if ($this->_page < $this->_max_pages) {
		    return '<span class="page_link"><a href="' . $this->_php_self . '?page=' . ($this->_page + 1) . '&row_pp='.$this->_rows_per_page.'&' . $this->_append . '">' .$tag. '</a></span>';		    
		} else {
		    return "<span class='page_link disabled'><a href='#'>$tag</a></span>";
		    
		}
	}
	
	/**
	 * Display the previous link
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to '<<'
	 * @return string
	 */
	public function renderPrev($tag = '&larr; Previous') {
		if ($this->_total_rows == 0)
			return FALSE;
		
		if ($this->_page > 1) {
		    return '<span class="page_link"><a href="' . $this->_php_self . '?page=' . ($this->_page - 1) . '&row_pp='.$this->_rows_per_page.'&' . $this->_append . '">' .$tag. '</a></span>';		    
		} else {
		    return "<span class='page_link disabled'><a href='#'>$tag</a></span>";		     
		}
	}
	
	/**
	 * Display the page links
	 *
	 * @access public
	 * @return string
	 */
	public function renderNav($prefix = '<span class="page_link">', $suffix = '</span>') {
		if ($this->_total_rows == 0)
			return FALSE;
		
		$batch = ceil($this->_page / $this->_links_per_page );
		$end = $batch * $this->_links_per_page;
		if ($end == $this->_page) {
			//$end = $end + $this->_links_per_page - 1;
			//$end = $end + ceil($this->_links_per_page/2);
		}
		if ($end > $this->_max_pages) {
			$end = $this->_max_pages;
		}
		$start = $end - $this->_links_per_page + 1;
		$links = '';
		
		for($i = $start; $i <= $end; $i ++) {
			if ($i == $this->_page) {
				$links .= $prefix . "<a href='#' class='active'>"." $i " ."</a>". $suffix;
			} else {
				$links .= $prefix . '<a href="' . $this->_php_self . '?page=' . $i .'&row_pp='.$this->_rows_per_page.'&' . $this->_append . '">' . $i . '</a>' . $suffix;
				//$links .= '<li><a href="' . $this->_php_self . '?page=' . $i . '&' . $this->_append . '">' . $i . '</a></li>';
			}
		}
		
		return $links;
	}
	
	/**
	 * Display full pagination navigation
	 *
	 * @access public
	 * @return string
	 */
	public function renderFullNav() {
		return '<span class="pContainer">'.$this->renderFirst() . $this->renderPrev() . $this->renderNav() . $this->renderNext() . $this->renderLast().'</span>';
	}
	
	/**
	 * Displays the page number
	 * 
	 * @return type pageNumber
	 */
	public function getPage(){
	    return $this->_page;
	}	
	
	public function getRowsPerPage(){
	    return $this->_rows_per_page;
	}

}
?>
