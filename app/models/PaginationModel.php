<?php

namespace models;

class PaginationModel
{

    public $current_page;
    public $records_per_page;
    public $total_pages;

    public function __construct($current_page = 1, $records_per_page = 6, $total_pages = 0)
    {
        $this->current_page = (int) $current_page;
        $this->records_per_page = (int) $records_per_page;
        $this->total_pages = (int) $total_pages;
    }

    public function offset()
    {
        return $this->records_per_page * ($this->current_page - 1);
    }

    public function totalPages()
    {
        return ceil($this->total_pages / $this->records_per_page);
    }
    public function nextPage()
    {
        if ($this->current_page < $this->totalPages()) {
            return $this->current_page + 1;
        }
        return false;
    }
    public function previousPage()
    {

        if ($this->current_page > 1) {
            return $this->current_page - 1;
        }
        return false;
    }

    public function previousLink()
    {
        $html = "";
        if ($this->previousPage() !== false) {
            $html .= '<a class="pagination_link" href="?page=' . $this->previousPage() . '">previous</a>';
        }
        return $html;
    }

    public function nextLink()
    {
        $html = "";
        if ($this->nextPage() !== false) {
            $html .= '<a class="pagination_link" href="?page=' . $this->nextPage() . '">next</a>';
        }
        return $html;
    }

    public function pagination()
    {

        $html = "";
        if ($this->totalPages() > 1) {
            // Pagination logic would go here
            $html .= '<div class="pagination">';
            $html .= $this->previousLink();
            $html .= $this->pageNumbers();
            $html .= $this->nextLink();
            $html .= '</div>';
        }
        return $html;
    }

    public function pageNumbers()
    {
        $html = "";
        for ($i = 1; $i <= $this->totalPages(); $i++) {
            if ($i == $this->current_page) {
                $html .= "<span class='pagination_active'>" . $i . "</span>";
            } else {
                $html .= "<a class='pagination_link' href='?page=" . $i . "'>" . $i . "</a>";
            }
        }
        return $html;
    }
}
