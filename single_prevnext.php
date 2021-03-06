<?php
/**
 * 記事ページで、祖先カテゴリーを対象に前後リンクを作る
 * wordpress version 5.0.3 で動作確認済
 */

// 現在の投稿IDを取得する
$current_id = $post -> ID;

// 現在の投稿のカテゴリーのターム（id）を取得する
$category = get_the_category($current_id);
$category = $category[0];
$category_id = $category -> term_id;
$ancestors = get_ancestors($category_id, "category");

// 現在の投稿の祖先のカテゴリーのターム（id）を取得する（なければ現在のカテゴリーの投稿のターム）
if (count($ancestors) > 0) {
  $ancestor_id = array_reverse($ancestors);
  $ancestor_id = $ancestor_id[0];
} else {
  $ancestor_id = $category_id;
}

// 祖先の名前を取得する
$ancestor_name = get_the_category_by_ID($ancestor_id);

// 祖先のターム（id）を配列にする
$array_category = array($ancestor_id);

// 子孫のターム（id）を取得する
$categories = get_categories(array(
  'child_of' => $ancestor_id
));

// 祖先配列に、子孫のターム（id）を追加する
foreach ($categories as $cat) {
  $array_category[] = $cat -> term_id;
}

// 前後IDを取得する
$args = array(
  'post_type' => 'post',
  'category__in' => $array_category,
  'posts_per_page' => -1,
  'order' => 'DESC'
);
$posts = get_posts( $args );
if ($post) {

  $is_next = false;
  $next_id = "";
  $prev_id = "";

  foreach ($posts as $post) {
    setup_postdata($post);

    $check_id = $post -> ID;

    if ($check_id == $current_id) {
      $is_next = true;
    } elseif (false === $is_next) {
      $next_id = $check_id;
    } elseif (true === $is_next) {
      $prev_id = $check_id;
      break;
    }
  }
}
wp_reset_postdata(); // 直前のクエリを復元する

// 前後記事のどちらかがある場合は、コーディングする
if ("" != $next_id || "" != $prev_id):
?>
  <h1><?php echo $ancestor_name; ?>に関連する前後記事</h1>

  <?php if ("" != $prev_id): $nextprev_id = $prev_id; ?>
  <h2>前の記事情報</h2>
  <ul>
    <li>タイトル：<?php echo get_the_title($nextprev_id); ?></li>
    <li>URL：<?php echo get_the_permalink($nextprev_id); ?></li>
    <li>日付：<?php echo get_the_date("Y-m-d H:i", $nextprev_id); ?></li>
    <li>サムネイル：<?php if (get_the_post_thumbnail_url($nextprev_id)) { echo get_the_post_thumbnail_url($nextprev_id); }; ?></li>
    <?php
    $category = get_the_category($nextprev_id);
    $category = $category[0];
    $category_name = $category->name;
    $category_base = $category->slug;
    ?>
    <li>カテゴリー名：<?php echo $category_name; ?></li>
    <li>カテゴリースラッグ：<?php echo $category_base; ?></li>
  </ul>
  などなど、よしなに
  <?php endif; ?>

  <?php if ("" != $next_id): $nextprev_id = $next_id; ?>
  <h2>次の記事情報</h2>
  <ul>
    <li>タイトル：<?php echo get_the_title($nextprev_id); ?></li>
    <li>URL：<?php echo get_the_permalink($nextprev_id); ?></li>
    <li>日付：<?php echo get_the_date("Y-m-d H:i", $nextprev_id); ?></li>
    <li>サムネイル：<?php echo (get_the_post_thumbnail_url($nextprev_id))? get_the_post_thumbnail_url($nextprev_id): "なし"; ?></li>
    <?php
    $category = get_the_category($nextprev_id);
    $category = $category[0];
    $category_name = $category->name;
    $category_base = $category->slug;
    ?>
    <li>カテゴリー名：<?php echo $category_name; ?></li>
    <li>カテゴリースラッグ：<?php echo $category_base; ?></li>
  </ul>
  などなど、よしなに
  <?php endif; ?>

<?php endif; ?> 
  
