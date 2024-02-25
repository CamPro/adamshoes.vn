<?php

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}


function index_get_new_articles()
{
    $sql = 'SELECT a.article_id, a.title, ac.cat_name, a.add_time, a.article_small_thumb, a.file_url, a.open_type, ac.cat_id, ac.cat_name ' .
            ' FROM ' . $GLOBALS['ecs']->table('article') . ' AS a, ' .
                $GLOBALS['ecs']->table('article_cat') . ' AS ac' .
            ' WHERE a.is_open = 1 AND a.cat_id = ac.cat_id AND ac.cat_type = 1' .
            ' ORDER BY a.add_time DESC LIMIT ' . $GLOBALS['_CFG']['article_number'];
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res AS $idx => $row)
    {
        $arr[$idx]['id']          = $row['article_id'];
        $arr[$idx]['title']       = $row['title'];
        $arr[$idx]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ?
                                        sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
        $arr[$idx]['cat_name']    = $row['cat_name'];
        $arr[$idx]['add_time']    = timeAgo(date('d-m-Y H:i:s', $row['add_time']));
        $arr[$idx]['thumb']       = !empty($row['article_small_thumb']) ? $row['article_small_thumb'] : 'images/no_picture.gif';
        $arr[$idx]['url']         = $row['open_type'] != 1 ?
                                        build_uri('article', array('aid' => $row['article_id']), $row['title']) : trim($row['file_url']);
        $arr[$idx]['cat_url']     = build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']);
    }

    return $arr;
}

/**
 * 获得文章分类下的文章列表
 *
 * @access  public
 * @param   integer     $cat_id
 * @param   integer     $page
 * @param   integer     $size
 *
 * @return  array
 */
function get_cat_articles($cat_id, $page = 1, $size = 20 ,$requirement='')
{
    if ($cat_id == '-1')
    {
        $cat_str = 'cat_id > 0';
    }
    else
    {
        $cat_str = get_article_children($cat_id);
    }
    if ($requirement != '')
    {
        $sql = 'SELECT article_id, title, article_type, description, article_thumb, article_small_thumb, author, add_time, viewed' .
               ' FROM ' .$GLOBALS['ecs']->table('article') .
               ' WHERE is_open = 1 AND title like \'%' . $requirement . '%\' ' .
               ' ORDER BY add_time DESC';
    }
    else
    {

        $sql = 'SELECT article_id, title, article_type, description, article_thumb,article_small_thumb, author, add_time, viewed' .
               ' FROM ' .$GLOBALS['ecs']->table('article') .
               ' WHERE is_open = 1 AND ' . $cat_str .
               ' ORDER BY add_time DESC';
    }

    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page-1) * $size);

    $arr = array();
    if ($res)
    {
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $article_id = $row['article_id'];

            $arr[$article_id]['id']          = $article_id;
            $arr[$article_id]['title']       = $row['title'];
            $arr[$article_id]['desc']        = $row['description'];
            $arr[$article_id]['viewed']      = $row['viewed'];
            $arr[$article_id]['is_hot']      = $row['article_type'];
            $arr[$article_id]['thumb']       = !empty($row['article_thumb'])? $row['article_thumb'] :'images/no_picture.gif';
            $arr[$article_id]['small_thumb'] = !empty($row['article_small_thumb'])? $row['article_small_thumb'] :'images/no_picture.gif';
            $arr[$article_id]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ? sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
            $arr[$article_id]['author']      = empty($row['author']) || $row['author'] == '_SHOPHELP' ? $GLOBALS['_CFG']['shop_name'] : $row['author'];
            $arr[$article_id]['url']         = build_uri('article', array('aid'=>$article_id), $row['title']);
            $arr[$article_id]['add_time']    = timeAgo(date('d-m-Y H:i:s', $row['add_time']));
        }
    }

    return $arr;
}


/**
 * 5 tin duoc danh dau la HOT
 *
 * @access  public
 * @param   integer     $cat_id
 *
 * @return  array
 */
function get_top5_articles($cat_id)
{

    if ($cat_id == '-1')
    {
        $cat_str = 'cat_id > 0';
    }
    else
    {
        $cat_str = get_article_children($cat_id);
    }
    $sql = 'SELECT * FROM (SELECT article_id, title, description, article_thumb,  article_small_thumb, author, add_time, viewed' .
               ' FROM ' .$GLOBALS['ecs']->table('article') .
               ' WHERE is_open = 1 AND article_type = 1 AND ' . $cat_str .
               ' ORDER BY add_time DESC LIMIT 5) TB ORDER BY RAND()';

    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    if ($res)
    {
        foreach ($res AS $row)
        {
            $article_id = $row['article_id'];

            $arr[$article_id]['id']          = $article_id;
            $arr[$article_id]['title']       = $row['title'];
            $arr[$article_id]['desc']        = $row['description'];
            $arr[$article_id]['viewed']      = $row['viewed'];

            $arr[$article_id]['thumb']       = !empty($row['article_thumb'])? $row['article_thumb'] :'images/no_picture.gif';
            $arr[$article_id]['sthumb']      = !empty($row['article_small_thumb'])? $row['article_small_thumb'] :'images/no_picture.gif';

            $arr[$article_id]['short_title']  = $GLOBALS['_CFG']['article_title_length'] > 0 ? sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
            $arr[$article_id]['author']      = empty($row['author']) || $row['author'] == '_SHOPHELP' ? $GLOBALS['_CFG']['shop_name'] : $row['author'];
            $arr[$article_id]['url']         = $row['open_type'] != 1 ? build_uri('article', array('aid'=>$article_id), $row['title']) : trim($row['file_url']);
            $arr[$article_id]['add_time']    = timeAgo(date('d-m-Y H:i:s', $row['add_time']));
        }
    }

    return $arr;
}

/**
 * lay 5 tin khuyen mai
 *
 * @access  public
 * @param   integer     $cat_id
 *
 * @return  array
 */
function get_promo_articles()
{
    $sql = 'SELECT article_id, title, article_thumb, add_time' .
               ' FROM ' .$GLOBALS['ecs']->table('article') .
               ' WHERE is_open = 1 AND cat_id = 10'.
               ' ORDER BY add_time DESC LIMIT 5';

    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    if ($res)
    {
        foreach ($res AS $row)
        {
            $article_id = $row['article_id'];
            $arr[$article_id]['id']          = $article_id;
            $arr[$article_id]['title']       = $row['title'];
            $arr[$article_id]['thumb']       = !empty($row['article_thumb'])? $row['article_thumb'] :'images/no_picture.gif';
            $arr[$article_id]['url']         = build_uri('article', array('aid'=>$article_id), $row['title']);
        }
    }

    return $arr;
}
/**
 * 10 tin xem nhieu nhat
 *
 * @access  public
 * @param   integer     $cat_id
 *
 * @return  array
 */
function get_top10viewed_articles($cat_id)
{
    if ($cat_id == '-1')
    {
        $cat_str = 'cat_id > 0';
    }
    else
    {
        $cat_str = get_article_children($cat_id);
    }
    $sql = 'SELECT article_id, title, add_time, viewed' .
               ' FROM ' .$GLOBALS['ecs']->table('article') .
               ' WHERE is_open = 1 AND ' . $cat_str .
               ' ORDER BY viewed DESC,add_time DESC LIMIT 5';

    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    if ($res)
    {
        foreach ($res AS $row)
        {
            $article_id = $row['article_id'];
            $arr[$article_id]['title']       = $row['title'];
            $arr[$article_id]['viewed']      = $row['viewed'];
            $arr[$article_id]['url']         = build_uri('article', array('aid'=>$article_id), $row['title']);
            $arr[$article_id]['add_time']    = timeAgo(date('d-m-Y H:i:s', $row['add_time']));
        }
    }

    return $arr;
}

/**
 * 获得指定分类下的文章总数
 *
 * @param   integer     $cat_id
 *
 * @return  integer
 */
function get_article_count($cat_id ,$requirement='')
{
    global $db, $ecs;
    if ($requirement != '')
    {
        $count = $db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('article') . ' WHERE ' . get_article_children($cat_id) . ' AND  title like \'%' . $requirement . '%\'  AND is_open = 1');
    }
    else
    {
        $count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('article') . " WHERE " . get_article_children($cat_id) . " AND is_open = 1");
    }
    return $count;
}

function cat_vieclam($parent_id)
{
        /* 获取当前分类及其父分类 */
    $sql = 'SELECT cat_id, cat_name FROM ' . $GLOBALS['ecs']->table('article_cat') .
                "WHERE parent_id = '$parent_id' AND cat_type = 1 ORDER BY sort_order ASC";
    $res = $GLOBALS['db']->getAll($sql);

      $arr = array();
   foreach ($res AS $idx => $row)
    {
        $arr[$idx]['cat_name'] = $row['cat_name'];
        $arr[$idx]['cat_url']  = build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']);
        $arr[$idx]['count'] = count_dangtuyen($row['cat_id']);
    }
    return $arr;
}
function count_dangtuyen($cat_id){
    $sql = "SELECT COUNT(*) FROM ". $GLOBALS['ecs']->table('article'). " WHERE cat_id = '$cat_id' AND is_open = 1";
    $count = $GLOBALS['db']->getOne($sql);
    return $count;
}

?>