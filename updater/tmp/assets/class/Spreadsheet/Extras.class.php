<?php

class MediaXLSX_Extras extends MediaXLSX 
{

	public static function addFormTag(&$sheet_obj, $index, $fnumber, $fletter, $fdetails, $fconfig, $box_details)
	{

		$sheet_idx = $sheet_obj->getSheet(0);

		$page_details = array();
		$pos = array();

		$cols = array(
			"left" => array("B", "C"),
			"right" => array("E", "F")
		);




		$pos["Form"] = $fnumber . $fletter;
		if (
			$box_details['packaging'] == "half" ||
			$box_details['packaging'] == "full"
		) {
			$packaging = $box_details['packaging'] . " skid";
			$skid = true;
		} else {
			$packaging = $box_details['packaging'];
			$skid = false;
		}
		$pos["Packaging"] = Ucwords($packaging);
		$pos["Lift Size"] = $box_details['lift_size'];

		if ($skid == true) {
			$pos["Lifts per Layer"] = $box_details['lifts_per_layer'];
			$pos["_2"] = "";
			$pos["Layers"] = $box_details['layers_last_box'];
			$pos["Lifts"] = $box_details['lifts_last_layer'];
		} else {
			$pos["__2"] = "";
			$pos["Full Cartons"] = $box_details['full_boxes'];
			$pos["Last Carton"] = $box_details['lifts_last_layer'];
			$pos["Total Cartons"] = $box_details['full_boxes'] + 1;
		}

		$pos["__3"] = "";
		$pos["Total"] = $fdetails['count'];


		if ($index % 4 == 0 || $index % 2 == 0) {
			$pos_side = "right";
		} else {
			$pos_side = "left";
		}

		$cell_label_col = $cols[$pos_side][0];
		$cell_value_col = $cols[$pos_side][1];




		$cell_row_label = array();
		$cell_row_value = array();

		$old_row = self::$cell_row;



		foreach ($pos as $row_label => $row_data) {
		
			if (!str_starts_with($row_label, "_")) {
				if ($row_label == "Form") {
					$cell_row_value[$cell_label_col . self::$cell_row] = $row_data;
					goto cont;
				}

				if ($row_label == "Total") {
					$sheet_idx->getStyle($cell_value_col . self::$cell_row)->getNumberFormat()->setFormatCode('#,##0');
					$total_cell_row = self::$cell_row;
				}

				$cell_row_label[$cell_label_col . self::$cell_row] = $row_label;
				$cell_row_value[$cell_value_col . self::$cell_row] = $row_data;
			}

			cont:
			self::$cell_row = self::$cell_row + 1;

		}


		$page_details = array($cell_row_label, $cell_row_value);
		$range = $cell_label_col . $old_row . ':' . $cell_value_col . self::$cell_row;

		self::$cell_row = $old_row;
		$type = ((($index % 6) == 0) ? 1 : 0);

		foreach ($page_details as $key_idxb => $value_array) {
			foreach ($value_array as $label => $cell_value) {
				$sheet_idx->setCellValue($label, $cell_value);
				$sheet_idx->getStyle($label)->getFont()->setSize("18");

				if (str_starts_with($label, "B")) {
					$sheet_idx->getStyle($label)->getFont()->setBold(false);
					$sheet_idx->getStyle($label)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
					continue;
				}
				if (str_starts_with($label, "E")) {
					$sheet_idx->getStyle($label)->getFont()->setBold(false);
					$sheet_idx->getStyle($label)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
					continue;
				}

				$sheet_idx->getStyle($label)->getFont()->setBold("true");
				$sheet_idx->getStyle($label)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			}
		}

		$sheet_idx->mergeCells($cell_label_col . $old_row . ":" . $cell_value_col . $old_row);
		$sheet_idx->getStyle($cell_label_col . $old_row . ":" . $cell_value_col . $old_row)->getFont()->setBold("true");
		$sheet_idx->getStyle($cell_label_col . $old_row . ":" . $cell_value_col . $old_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$last_row = $total_cell_row;
		if ($index % 2 == 0) {
			Excel_Styles::cellBorder($sheet_idx, 'A' . $last_row . ':F' . $last_row, "bottom");
			Excel_Styles::cellBorder($sheet_idx, 'D1:D' . $last_row, "LEFT");
			$cell_row = self::$cell_row + 10;
		}

		if ($type == 1) {
			$sheet_idx->setBreak('A' . $last_row, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		}
		$type = 0;
		#}	
		//$sheet_idx->setCellValue('P'.$index,var_export($box_details,1));


	}


	public static function masterListHeader(&$sheet_obj)
	{
		$index = 1;
		$sheet_idx = $sheet_obj->getSheet(0);
		$sheet_idx->setCellValue('A' . $index, "Form Number");

		$sheet_idx->setCellValue('C' . $index, 'market');
		$sheet_idx->setCellValue('D' . $index, 'pub');
		$sheet_idx->setCellValue('E' . $index, 'ship');
		$sheet_idx->setCellValue('F' . $index, 'count');

		$sheet_idx->setCellValue('H' . $index, 'configuration');
		$sheet_idx->setCellValue('I' . $index, 'paper_wieght');
		$sheet_idx->setCellValue('J' . $index, 'packaging');
		$sheet_idx->setCellValue('K' . $index, 'Full,layers,lifts');

		$letters = range('A', 'M');
		foreach ($letters as $col) {
			$sheet_idx->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			//$sheet_idx->getColumnDimension($col)->setWidth(90);
			$sheet_idx->getColumnDimension($col)->setAutoSize(true);
		}
	}

	public static function createMasterList($sheet_obj, $index, $fnumber, $fletter, $fdetails, $fconfig, $box_details)
	{
		$bindery_destination = "";
		$sheet_idx = $sheet_obj->getSheet(0);


		if ($index % 2 == 0) {
			$sheet_idx->getStyle('A' . $index . ':M' . $index)->getFill()
				->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()->setARGB('00C1FFF3');
		}

		$sheet_idx->setCellValue('A' . $index, $fnumber . $fletter . " - " . $fdetails['former']);
		$sheet_idx->getStyle('F' . $index)->getNumberFormat()->setFormatCode('#,##0');


		$sheet_idx->setCellValue('C' . $index, $fdetails['market']);
		$sheet_idx->setCellValue('D' . $index, $fdetails['pub']);
		$sheet_idx->setCellValue('E' . $index, $fdetails['ship']);
		$sheet_idx->setCellValue('F' . $index, $fdetails['count']);


		$sheet_idx->setCellValue('H' . $index, $fconfig['configuration'] . $bindery_destination);
		$sheet_idx->setCellValue('I' . $index, $fconfig['paper_wieght']);

		$sheet_idx->setCellValue('J' . $index, $box_details['packaging']);
		$sheet_idx->setCellValue('K' . $index, $box_details['full_boxes'] . "," . $box_details['layers_last_box'] . "," . $box_details['lifts_last_layer']);

		//$sheet_idx->setCellValue('P'.$index,var_export($box_details,1));
	}

}