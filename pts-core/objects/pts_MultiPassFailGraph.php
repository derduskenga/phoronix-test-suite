<?php

/*
	Phoronix Test Suite "Trondheim"
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2008, Phoronix Media
	Copyright (C) 2008, Michael Larabel
	pts_PassFailGraph.php: An abstract graph object extending pts_Graph for showing results in a pass/fail scenario.
*/

class pts_MultiPassFailGraph extends pts_CustomGraph
{
	public function __construct($Title, $SubTitle, $YTitle)
	{
		$this->graph_y_title_hide = TRUE;
		parent::__construct($Title, $SubTitle, $YTitle, true);
		$this->graph_type = "MULTI_PASS_FAIL";
		$this->graph_value_type = "ABSTRACT";
		$this->graph_hide_identifiers = TRUE;
	}
	protected function render_graph_passfail()
	{
		$identifier_count = count($this->graph_identifiers);
		$vertical_border = 20;
		$horizontal_border = 14;
		$heading_height = 24;
		$graph_width = $this->graph_left_end - $this->graph_left_start - ($horizontal_border * 2);
		$graph_height = $this->graph_top_end - $this->graph_top_start - ($vertical_border * 2) - $heading_height;
		$line_height = floor($graph_height / $identifier_count);

		$pass_color = $this->next_paint_color();
		$fail_color = $this->next_paint_color();

		$main_width = floor($graph_width * .3);
		$main_font_size = $this->graph_font_size_bars;
		$main_greatest_length = $this->find_longest_string($this->graph_identifiers);
		while(($this->return_ttf_string_width($main_greatest_length, $this->graph_font, $main_font_size) > ($main_width - 8)) || $this->return_ttf_string_height($main_greatest_length, $this->graph_font, $main_font_size) > ($line_height - 4))
			$main_font_size -= 0.5;

		if(($new_size = $this->return_ttf_string_width($main_greatest_length, $this->graph_font, $main_font_size)) < ($main_width - 12))
			$main_width = $new_size + 10;

		$identifiers_total_width = $graph_width - $main_width - 2;

		$headings = explode(",", $this->graph_y_title);
		$identifiers_width = floor($identifiers_total_width / count($headings));
		$headings_font_size = $this->graph_font_size_bars;
		while(($this->return_ttf_string_width($this->find_longest_string($headings), $this->graph_font, $headings_font_size) > ($identifiers_width - 2)) || $this->return_ttf_string_height($this->graph_maximum_value, $this->graph_font, $headings_font_size) > ($line_height - 4))
			$headings_font_size -= 0.5;

		for($j = 0; $j < count($this->graph_data[0]); $j++)
		{
			$results = array_reverse(explode(",", $this->graph_data[0][$j]));
			$line_ttf_height = $this->return_ttf_string_height("AZ@![]()@|_", $this->graph_font, $this->graph_font_size_bars);
			for($i = 0; $i < count($headings) && $i < count($results); $i++)
			{
				if($results[$i] == "PASS")
					$paint_color = $pass_color;
				else
					$paint_color = $fail_color;

				$this_bottom_end = $this->graph_top_start + $vertical_border + (($j + 1) * $line_height) + $heading_height + 1;

				if($this_bottom_end >= $this->graph_top_end - $vertical_border)
					$this_bottom_end = $this->graph_top_end - $vertical_border - 1;

				imagefilledrectangle($this->graph_image, $this->graph_left_end - $horizontal_border - ($i * $identifiers_width), $this->graph_top_start + $vertical_border + ($j * $line_height) + $heading_height, $this->graph_left_end - $horizontal_border - (($i + 1) * $identifiers_width), $this_bottom_end, $paint_color);
				$this->gd_write_text_center($results[$i], $this->graph_font_size_bars, $this->graph_color_body_text, $this->graph_left_end - $horizontal_border - ($i * $identifiers_width) - ($identifiers_width / 2), $this->graph_top_start + $vertical_border + ($j * $line_height) + $heading_height + ($line_height / 2) - ($line_ttf_height / 2));
			}
		}

		$headings = array_reverse($headings);
		$line_ttf_height = $this->return_ttf_string_height("AZ@![]()@|_", $this->graph_font, $headings_font_size);
		for($i = 0; $i < count($headings); $i++)
		{
			imageline($this->graph_image, $this->graph_left_end - $horizontal_border - (($i + 1) * $identifiers_width), $this->graph_top_start + $vertical_border, $this->graph_left_end - $horizontal_border - (($i + 1) * $identifiers_width), $this->graph_top_end - $vertical_border, $this->graph_color_body_light);
			$this->gd_write_text_center($headings[$i], $headings_font_size, $this->graph_color_headers, $this->graph_left_end - $horizontal_border - ($i * $identifiers_width) - ($identifiers_width / 2), $this->graph_top_start + $vertical_border + ($heading_height / 2) - ($line_ttf_height / 2));
		}

		$line_ttf_height = $this->return_ttf_string_height("AZ@![]()@|_", $this->graph_font, $main_font_size);
		for($i = 0; $i < count($this->graph_identifiers); $i++)
		{
			imageline($this->graph_image, $this->graph_left_start + $horizontal_border, $this->graph_top_start + $vertical_border + ($i * $line_height) + $heading_height, $this->graph_left_end - $horizontal_border, $this->graph_top_start + $vertical_border + ($i * $line_height) + $heading_height, $this->graph_color_body_light);
			$this->gd_write_text_right($this->graph_identifiers[$i], $main_font_size, $this->graph_color_headers, $this->graph_left_start + $horizontal_border + $main_width, $this->graph_top_start + $vertical_border + ($i * $line_height) + $heading_height + ($line_height / 2) - 2, FALSE);
		}

		imageline($this->graph_image, $this->graph_left_start + $horizontal_border, $this->graph_top_start + $vertical_border, $this->graph_left_end - $horizontal_border, $this->graph_top_start + $vertical_border, $this->graph_color_body_light);
		imageline($this->graph_image, $this->graph_left_start + $horizontal_border, $this->graph_top_start + $vertical_border, $this->graph_left_start + $horizontal_border, $this->graph_top_end - $vertical_border, $this->graph_color_body_light);
		imageline($this->graph_image, $this->graph_left_end - $horizontal_border, $this->graph_top_start + $vertical_border, $this->graph_left_end - $horizontal_border, $this->graph_top_end - $vertical_border, $this->graph_color_body_light);
		imageline($this->graph_image, $this->graph_left_start + $horizontal_border, $this->graph_top_end - $vertical_border, $this->graph_left_end - $horizontal_border, $this->graph_top_end - $vertical_border, $this->graph_color_body_light);
	}
	protected function render_graph_result()
	{
		$this->render_graph_passfail();
	}
}

?>
