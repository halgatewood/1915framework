<?php 	

/**
 * 		1915framework -	mySQL Class
 * 		based on the ezSQL class created by Justin Vincent (http://www.justinvincent.com)
 */
 
if(!function_exists("site_unavailable"))
{
	function site_unavailable()
	{
		require_once( dirname(__FILE__) . "/../templates/internal_error.html");
		die;
	}
}

class mysql
{
		var $db = array();
		var $errors = array();		
		var $querylist = array();
		
		function mysql($db)
		{
			$this->db = $db;
			$this->connect();
		}

	    // ==================================================================
		//	Connect 
		
		function connect() 
		{
			$this->dbh = @mysql_connect($this->db['host'], $this->db['user'], $this->db['pass']) or site_unavailable();;
	
	        if (!$this->dbh)
	        {
	        	return false;
	        }
	        
	        @mysql_select_db($this->db['name'], $this->dbh) or site_unavailable();

			$this->query("SET character_set_results=utf8");
				
			mb_language('uni');
			mb_internal_encoding('UTF-8');
	        
	        $this->query("SET names 'utf8'");
	        $this->query("SET character_set_results=utf8");
	        $this->query("SET character_set_client=utf8");
			$this->query("SET character_set_connection=utf8");
	    }

	    
	    // ==================================================================
		//	Query 
		
		function query($query) 
		{
			$this->last_result = null;
			$this->col_info = null;
			$this->insert_id = null;
			$this->count = 0;
			$this->last_query = $query;
			
			$this->result = mysql_query($query,$this->dbh);
			$this->addToQueryList($query);
			
			$this->insert_id = mysql_insert_id( );
	
			if ( mysql_error() ) 
			{
				$this->addError();
			}
			else
			{
				// If select statement
				if ( $this->result )
				{
					// Take note of column info
					$i=0;
					while ($i < @mysql_num_fields($this->result))
					{
						$this->col_info[$i] = @mysql_fetch_field($this->result);
						$i++;
					}

					// Store Query Results
					$i=0;
					while ( $row = @mysql_fetch_object($this->result) )
					{ 
						$this->last_result[$i] = $row;
						$i++;
					}
					$this->count = $i;
					
					@mysql_free_result($this->result);
	
					if ( $i )
					{
						return true;
					}
					else
					{
						return false;
					}
				}
			}
		}
		
		// ==================================================================
		//	Query and return ID
		function query_to_id( $query, $column, $table )
		{
			$this->query("START TRANSACTION;");
			$this->query( $query );
			$this->query("SET @KEY = LAST_INSERT_ID();");
			$id = $this->get_var( "SELECT $column FROM $table WHERE $column = @KEY" );
			$this->query("COMMIT;");
			return $id;
		}
		
		
		
		
		
		// ==================================================================
		//	Get one variable from the DB
		
		function get_var($query = null, $x=0, $y=0)
		{
			if ( $query )
			{
				$this->query($query);
			}

			if ( $this->last_result[$y] )
			{
				$values = array_values(get_object_vars($this->last_result[$y]));
			}
			return $values[$x]?$values[$x]:null;
		}
		
	
		// ==================================================================
		//	Get one row from the DB
		
		function get_row($query=null, $y=0)
		{
			if ( $query )
			{
				$this->query($query);
			}
			
			return $this->last_result[$y]?$this->last_result[$y]:null;
		}
		
	
		// ==================================================================
		//	Function to get one column from the cached result set based in X index
	
		function get_col($query=null, $x=0)
		{
			if ( $query )
			{
				$this->query($query);
			}

			for ( $i=0; $i < count($this->last_result); $i++ )
			{
				$new_array[$i] = $this->get_var(null,$x,$i);
			}
			
			return $new_array;
		}
		
	
		// ==================================================================
		// Return the the query as an array of objects
		
		function get_results($query=null)
		{
			if ( $query )
			{
				$this->query($query);
			}	
				
			if(!is_array($this->last_result))
			{
				$this->last_result = array();
			}
			
			return $this->last_result; 
		}
	
	
		// ==================================================================
		// Function to get column meta data info pertaining to the last query
		
		function get_col_info($info_type="name",$col_offset=-1)
		{
			if ( $this->col_info )
			{
				if ( $col_offset == -1 )
				{
					$i=0;
					foreach($this->col_info as $col )
					{
						$new_array[$i] = $col->{$info_type};
						$i++;
					}
					return $new_array;
				}
				else
				{
					return $this->col_info[$col_offset]->{$info_type};
				}
			}
		}
		
		
		// ==================================================================
		// Insert Statement
		// @table - Table name
		// @values - object or array of values to insert
		
		function insert($table, $values)
		{
			$column_names = array();
			$column_values = array();
			
			foreach($values as $column => $value)
			{
				$column_names[] = $column;
				$column_values[] = (is_int($value)) ? $value : "'" . addslashes(trim($value)) ."'";
			}
			
			$sql = "INSERT INTO $table (" . implode(", ", $column_names) . ") VALUES (" . implode(", ", $column_values) . ")";
			return $this->query($sql);
		}
		
		
		// ==================================================================
		// Update Statement
		// @table - Table name
		// @values - object or array of values to update
		// @where - object or array of values to update
		// @limit - limit
		
		function update($table, $values, $where = false, $limit = 1)
		{
			$update_columns = array();

			foreach($values as $column => $value)
			{
				$value = (is_int($value)) ? $value : "'" . addslashes(trim($value)) ."'";
				$update_columns[] = "$column = $value";
			}
			
			$sql = "UPDATE $table SET " . implode(", ", $update_columns);
			
			if($where)
			{
				$where_columns = array();
			
				foreach($where as $col => $val)
				{
					$val = (is_int($val)) ? $val : "'" . addslashes(trim($val)) ."'";
					$where_columns[] = "$col = $val";
				}
				
				$sql .=" WHERE " . implode(" AND ", $where_columns);
			}
			
			if($limit) { $sql .= " LIMIT $limit"; }
			return $this->query($sql);
		}

		// ==================================================================
		// Delete Statement
		// @table - Table name
		// @values - object or array of values to insert
		
		function delete($table, $where = false, $limit = 1)
		{
			$sql = "DELETE FROM $table";
			
			if($where)
			{
				$where_columns = array();
			
				foreach($where as $col => $val)
				{
					$val = (is_int($val)) ? $val : "'" . addslashes(trim($val)) ."'";
					$where_columns[] = "$col = $val";
				}
				
				$sql .=" WHERE " . implode(" AND ", $where_columns);
			}

			if($limit) { $sql .= " LIMIT $limit"; }
			return $this->query($sql);
		}
		
				
		// ==================================================================
		// List of all queries called on this instance.
		
		function addToQueryList($qry)
		{
			if(!is_array($this->querylist)) { $this->querylist = array(); }
			$this->querylist[] = $qry;
		}
		
		function getQueryList()
		{
			return $this->querylist;
		}
		
		
		function get_count( $table, $site_ids, $timestamp )
		{
			return $this->get_var( "SELECT count(site_id) as cnt FROM $table WHERE site_id IN ($site_ids) AND timestamp > $timestamp" );
		}
		
		
		// ==================================================================
		// Error Handeling
		
		function addError($error = null)
		{
			if ( !$error ) $error = mysql_error();
			$this->errors[] = $error;
		}
	}
?>