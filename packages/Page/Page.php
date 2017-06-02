<?php
namespace app\packages\Page;

class Page
{
    private $total;      //总记录
    private $pagesize;    //每页显示多少条
    private $limit;          //limit
    private $page;           //当前页码
    private $pagenum;      //总页码
    public $url;           //地址
    private $bothnum;      //两边保持数字分页的量

    //构造方法初始化
    public function __construct($now_page, $_total, $_pagesize)
    {
        $this->total    = $_total ? $_total : 1;
        $this->pagesize = $_pagesize;
        $this->pagenum  = ceil($this->total / $this->pagesize);
        $this->page     = max($now_page, 1);
        $this->url      = $this->setUrl();
        $this->bothnum  = 2;
    }

    //获取地址
    private function setUrl()
    {
        $_url = $_SERVER["REQUEST_URI"];
        $_par = parse_url($_url);
        if (isset($_par['query'])) {
            parse_str($_par['query'], $_query);
            unset($_query['page']);
            $_url = $_par['path'] . '?' . http_build_query($_query);
        } else {
            $_url = $_par['path'] . '?';
        }

        return urldecode($_url);
    }     //数字目录

    private function pageList()
    {
        $_pagelist = '';
        for ($i = $this->bothnum; $i >= 1; $i--) {
            $_page = $this->page - $i;
            if ($_page < 1) {
                continue;
            }
            $_pagelist .= ' <li><a href="' . $this->url . '&page=' . $_page . '">' . $_page . '</a></li> ';
        }
        $_pagelist .= ' <li class="active"><a href="javascript:void(0);">' . $this->page . '</a></li> ';
        for ($i = 1; $i <= $this->bothnum; $i++) {
            $_page = $this->page + $i;
            if ($_page > $this->pagenum) {
                break;
            }
            $_pagelist .= ' <li><a href="' . $this->url . '&page=' . $_page . '">' . $_page . '</a></li> ';
        }

        return $_pagelist;
    }

    //首页
    private function first()
    {
        if ($this->page > $this->bothnum + 1) {
            return ' <li><a href="' . $this->url . '">1</a></li><li><a href="javascript:void(0);">...</a></li>';
        }
    }

    //上一页
    private function prev()
    {
        if ($this->page == 1) {
            return '<li><a href="#">&laquo;</a></li>';
        }

        return ' <li><a href="' . $this->url . '&page=' . ($this->page - 1) . '">&laquo;</a></li> ';
    }

    //下一页
    private function next()
    {
        if ($this->page == $this->pagenum) {
            return '<li><a href="#">&raquo;</a></li>';
        }

        return ' <li><a href="' . $this->url . '&page=' . ($this->page + 1) . '">&raquo;</a></li> ';
    }

    //尾页
    private function last()
    {
        if ($this->pagenum - $this->page > $this->bothnum) {
            return '<li><a href="javascript:void(0);">...</a></li><li><a href="' . $this->url . '&page=' . $this->pagenum . '">' . $this->pagenum . '</a></li>';
        }
    }

    //总数
    private function total()
    {
        return '<li><a href="javascript:void(0);">总数：' . $this->total . '</a></li>';
    }

    //分页信息
    public function showpage($_show_total = true, $_page_one_show = false)
    {
        if ($this->pagenum == 1 && !$_page_one_show) {
            return '';
        }
        $_page = '<ul class="pagination">';
        if ($this->page != 1) {
            $_page .= $this->prev();
        }
        $_page .= $this->first();
        $_page .= $this->pageList();
        $_page .= $this->last();
        if ($this->pagenum != $this->page) {
            $_page .= $this->next();
        }
        if ($_show_total) {
            $_page .= $this->total();
        }
        $_page .= '</ul>';

        return $_page;
    }
}