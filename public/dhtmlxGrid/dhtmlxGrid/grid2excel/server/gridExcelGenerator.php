<?php

class gridExcelGenerator {

	public $headerHeight = 30;
	public $rowHeight = 20;

	public $fontFamily = 'Helvetica';
	public $headerFontSize = 9;
	public $gridFontSize = 9;
	
	public $strip_tags = false;

	public $bgColor = 'D1E5FE';
	public $lineColor = 'A4BED4';
	public $scaleOneColor = 'FFFFFF';
	public $scaleTwoColor = 'E3EFFF';
	public $textColor = '000000';

	public $headerLinesNum = 3;
	public $headerFileName = 'header.xls';
//	public $headerFileName = false;


	public $creator = 'DHTMLX LTD';
	public $lastModifiedBy = '';
	public $title = 'dhtmlxGrid';
	public $subject = '';
	public $dsc = '';
	public $keywords = '';
	public $category = '';
	
	private $footerColumns = Array();
	private $columns = Array();
	private $rows = Array();
	private $cellColors = Array();
	private $summaryWidth;
	private $profile;
	private $header = null;
	private $footer = null;

	public function printGrid($xml) {
		$this->headerParse($xml->head);
		$this->footerParse($xml->foot);
		$this->mainParse($xml);
		$this->rowsParse($xml->row);
		$this->printGridExcel();
	}


	private function setProfile() {
		switch ($this->profile) {
			case 'color':
				$this->bgColor = 'D1E5FE';
				$this->lineColor = 'A4BED4';
				$this->scaleOneColor = 'FFFFFF';
				$this->scaleTwoColor = 'E3EFFF';
				$this->textColor = '000000';
				break;
			case 'gray':
				$this->bgColor = 'E3E3E3';
				$this->lineColor = 'B8B8B8';
				$this->scaleOneColor = 'FFFFFF';
				$this->scaleTwoColor = 'EDEDED';
				$this->textColor = '000000';
				break;
			case 'bw':
				$this->bgColor = 'FFFFFF';
				$this->lineColor = '000000';
				$this->scaleOneColor = 'FFFFFF';
				$this->scaleTwoColor = 'FFFFFF';
				$this->textColor = '000000';
				break;
		}
	}
	
	
	private function mainParse($xml) {
		$this->profile = (string) $xml->attributes()->profile;
		$this->setProfile();
		if (!file_exists($this->headerFileName)) {
			$this->headerLinesNum = 0;
			$this->headerFileName = false;
		}
	}

	private function headerParse($header) {
		if (isset($header->column)) {
			$columns = $header->column;
			$summaryWidth = 0;
			$this->columns[0] = Array();
			foreach ($columns as $column) {
				$columnArr = Array();
				$columnArr['text'] = trim((string) $column);
				$columnArr['width'] = trim((string) $column->attributes()->width);
				$columnArr['type'] = trim((string) $column->attributes()->type);
				$columnArr['align'] = trim((string) $column->attributes()->align);
				if (isset($column->attributes()->colspan)) {
					$columnArr['colspan'] = (int) $column->attributes()->colspan;
				}
				if (isset($column->attributes()->rowspan)) {
					$columnArr['rowspan'] = (int) $column->attributes()->rowspan;
				}
				$columnArr['excel_type'] = (isset($column->attributes()->excel_type)) ? trim((String) $column->attributes()->excel_type) : "";
				$summaryWidth += $columnArr['width'];
				$this->columns[0][] = $columnArr;
			}
		} else {
			if (isset($header->columns)) {
				$columns = $header->columns;
				$summaryWidth = 0;
				$i = 0;
				foreach ($columns as $row) {
					$this->columns[$i] = Array();
					foreach ($row as $column) {
						$columnArr = Array();
						$columnArr['text'] = $this->strip(trim((string) $column));
						$columnArr['width'] = trim((string) $column->attributes()->width);
						$columnArr['type'] = trim((string) $column->attributes()->type);
						$columnArr['align'] = trim((string) $column->attributes()->align);
						if (isset($column->attributes()->colspan)) {
							$columnArr['colspan'] = (int) $column->attributes()->colspan;
						}
						if (isset($column->attributes()->rowspan)) {
							$columnArr['rowspan'] = (int) $column->attributes()->rowspan;
						}
						if ($i == 0) {
							$summaryWidth += $columnArr['width'];
							$columnArr['excel_type'] = (isset($column->attributes()->excel_type)) ? trim((String) $column->attributes()->excel_type) : "";
						}
						$this->columns[$i][] = $columnArr;
					}
					$i++;
				}
			}
		}
		$this->summaryWidth = $summaryWidth;
	}


	private function footerParse($footer) {
		if (isset($footer->columns)) {
			$columns = $footer->columns;
			$summaryWidth = 0;
			$i = 0;
			foreach ($columns as $row) {
				$this->footerColumns[$i] = Array();
				foreach ($row as $column) {
					$columnArr = Array();
					$columnArr['text'] = $this->strip(trim((string) $column));
					$columnArr['width'] = trim((string) $column->attributes()->width);
					$columnArr['type'] = trim((string) $column->attributes()->type);
					$columnArr['align'] = trim((string) $column->attributes()->align);
					if (isset($column->attributes()->colspan)) {
						$columnArr['colspan'] = (int) $column->attributes()->colspan;
					}
					if (isset($column->attributes()->rowspan)) {
						$columnArr['rowspan'] = (int) $column->attributes()->rowspan;
					}
					if ($i == 0) {
						$summaryWidth += $columnArr['width'];
					}
					$this->footerColumns[$i][] = $columnArr;
				}
				$i++;
			}
		}
	}


	private function rowsParse($rows) {
		$i = 0;
		foreach ($rows as $row) {
			$rowArr = Array();
			$cellColors = Array();
			$cells = $row->cell;
			foreach ($cells as $cell) {
				$rowArr[] = $this->strip(trim((string) $cell));
				$colors = Array();
				if (isset($cell->attributes()->bgColor)) {
					$colors['bg'] = (string) $cell->attributes()->bgColor;
				} else {
					if ($i%2 == 0) {
						$color = $this->scaleOneColor;
					} else {
						$color = $this->scaleTwoColor;
					}
					$colors['bg'] = $color;
				}
				if (isset($cell->attributes()->textColor)) {
					$colors['text'] = (string) $cell->attributes()->textColor;
				} else {
					$colors['text'] = $this->textColor;
				}
				$cellColors[] = $colors;
			}
			$this->rows[] = $rowArr;
			$this->cellColors[] = $cellColors;
			$i++;
		}
	}


	public function printGridExcel() {
		$this->wrapper = new gridExcelWrapper();
		$this->wrapper->createXLS($this->headerFileName, $this->headerLinesNum, $this->creator, $this->lastModifiedBy, $this->title, $this->subject, $this->dsc, $this->keywords, $this->category);
		$this->wrapper->headerPrint($this->columns, $this->summaryWidth, $this->headerHeight, $this->textColor, $this->bgColor, $this->lineColor, $this->headerFontSize, $this->fontFamily);

		for ($i = 0; $i < count($this->rows); $i++) {
			$this->wrapper->rowPrint($this->rows[$i], $this->cellColors[$i], $this->rowHeight, $this->lineColor, $this->gridFontSize, $this->fontFamily);
		}
		$this->wrapper->footerPrint($this->footerColumns, $this->headerHeight, $this->textColor, $this->bgColor, $this->lineColor, $this->headerFontSize, $this->fontFamily);
		$this->wrapper->outXLS($this->title);
	}

	private function strip($param) {
		if ($this->strip_tags == true) {
			$param = strip_tags($param);
		}
		return $param;
	}

}


?>