<?php
/**
 * This class if for handling csv files but instead of relying on fgetcsv it explodes
 * the string. This is similar to CsvImorter class found on php.net but it fixes 
 * values that begin with " or '.
 *
 * @TODO - deal with enclosures so we can use this call for everything
 */
class CsvExploder
{
  private $_fp;
  private $_parse_header;
  private $_header;
  private $_delimiter;
  private $_length;
  private $_enclosure;
  
  /**
   * Constructor
   */
  function __construct($file_name, $parse_header=false, $delimiter="\t", $length=10000, $enclosure = FALSE) 
  { 
    $this->_fp = fopen($file_name, "r"); 
    $this->_parse_header = $parse_header; 
    $this->_delimiter = $delimiter; 
    $this->_length = $length; 
    $this->_enclosure = $enclosure;
  }
  
  /**
   * Deconstructor
   */
  function __destruct() 
  { 
    $this->_file_close();
  }
  
  /**
   *  Parse through the file
   *
   * @param int $max_lines
   *  the number of lines to return
   */
  function get($max_lines = 0) 
  { 
    $line_count = 0;
    $data = array(); 
    
    if($max_lines > 0) {
      $line_count = 0; 
    }
    else { 
      $line_count = -1; // so loop limit is ignored
    }
    
    while ($line_count < $max_lines && ($line = fgets($this->_fp, $this->_length)) !== false) {
      if(!$this->_header && $this->_parse_header) {
        $header = explode($this->_delimiter, $line);
        foreach($header as $h) {
          $this->_header[] = trim($h);
        }
      }
      else {
        $row = explode($this->_delimiter, $line);
        
        if($this->_parse_header) {
          foreach($this->_header as $i => $heading_i) { 
            $row_new[$heading_i] = trim($row[$i]); 
          }
          $data[] = $row_new;
        }
        else {
          $data[] = $row; 
        }
      }
      
      $line_count++;
    } 
       
    return $data; 
  }
  
  /**
   * Close file
   */
  private function _file_close() {
    if($this->_fp) { 
      fclose($this->_fp); 
    }
  }
}