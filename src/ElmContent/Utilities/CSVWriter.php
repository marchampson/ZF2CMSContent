<?php
namespace ElmContent\Utilities;
/*$fout = new CSVWriter();
$fout->open( "hello.txt" );
$fout->writeHeader(array("col1","col2","col3") );
$fout->writeLine(array(1,2,3) );
$fout->writeLine(array(4,5,6) );
$fout->flush();
$fout->close();*/

class CSVWriter 
{
	
		public $handle;
		public $colCount;
		
		function open( $filename ){
			$this->handle = fopen( $filename, "w+");
		}

		function writeHeader( $names ){
			if (!is_array($names)) {
				$names = array($names);
			}
			$this->colCount=count($names);
			$i=0;
			foreach ($names as $name) {
				if($i++==0) $strOut=$name;
				else		$strOut=','.$name;
				$this->write( $strOut );
			}
        }
		
		function writeLine( $data ){
			if (!is_array($data)) {
				$data = array($data);
			}
			$this->write( "\n" );
			for( $i=0;$i<$this->colCount;$i++){			
				if($i<(count($data))) {
					if($i==0) $strOut='"'.$data[$i].'"';
					else		$strOut=',"'.$data[$i].'"';
				}else{
					$strOut = ',';
				}
				$this->write( $strOut );
			}
		}

		function write( $strOut ){
			if(FALSE===fwrite( $this->handle, $strOut )) throw exception("Failed to write to file");
		}
		
		function flush( ){
			fflush($this->handle);
		}

		function close( ){
			fclose( $this->handle );
		}
		
}