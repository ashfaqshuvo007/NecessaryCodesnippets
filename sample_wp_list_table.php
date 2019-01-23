<?php
//Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// WP_List_Table class loaded 
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class Sc_content_table extends WP_List_Table{
    
    //preparing items for representation
    public function  prepare_items()
    {
        //getting order & orderby for sortable from url
        $order_by = isset($_GET['orderby']) ? trim($_GET['orderby']) : "";
        $order = isset($_GET['order']) ? trim($_GET['order']) : "";
        
        //For search option
        $search_text = isset($_POST['s']) ? trim($_POST['s']) : "";

        /**Needed For Pagination */
        $all_data = $this->sc_content_table_data($order_by, $order, $search_text);
        $per_page = 10;
        $total_items = count($all_data);
        $current_page = $this->get_pagenum();
        
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page
        ));

        /**End for pagination */



        //getting all data
        $this->items = array_slice($all_data,(($current_page-1)*$per_page),$per_page);

        $columns = $this->get_columns();
        
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns,$hidden,$sortable);
        /**End formating data coming from the prepare items method */
    }

    //Data getting ready for sortable
    public function sc_content_table_data($order_by= " ", $order = " ", $search_text = " "){
        global $wpdb;

        //Search block
        if(!empty($search_text)){
            $data = $wpdb->get_results
                    ("SELECT * FROM {$wpdb->prefix}sc_content WHERE `sc_name` LIKE '%$search_text%' 
                        OR `shortcode`LIKE '%$search_text%' 
                        OR `content` LIKE '%$search_text%' 
                        OR `short_desc` LIKE '%$search_text%'
                        ",
                    ARRAY_A);     
                                    
            // var_dump($data);die();
        }else{
            if ($order_by == "Title" && $order = "asc") {
                $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sc_content ORDER BY `sc_name` ASC", ARRAY_A);
            } elseif ($order_by == "Title" && $order = "desc") {
                $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sc_content ORDER BY `sc_name` DESC", ARRAY_A);
            } else {
                $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sc_content", ARRAY_A);
            }
        }
        return $data;  
    }

    //for bulk actions
    public function get_bulk_actions(){
        $actions = array(
            'edit' => 'Edit',
            'delete' => 'Delete'
        );

        return $actions;
    }

    //For echeck box columns
    public function column_cb($item){

        return sprintf('<input type="checkbox" name="sc_content[]" value="%s"/>',$item['id']);

    }




    // overrriding the parent method
    public function get_columns()
    {
        $columns = array(
            'cb' => "<input type='checkbox'/>",
            'id' => 'ID',
            'sc_name' => 'Title',
            'content' => 'Content',
            'shortcode' => 'Shortcode',
            'short_desc' => 'Description',
            'created_at' => 'Created_at',
            'updated_at' => 'Updated_at',
            
        );
        return $columns;
    }

    //getting the hidden columns
    public function get_hidden_columns()
    {
        return array('id','created_at','updated_at');

    }

    //getting the sortable columns
    public function get_sortable_columns()
    {
        return array(
            // 'id' => array('ID',true),
             'sc_name' => array('Title', true),
        );

    }

      


    //To write markup at the top of the table
    // function extra_tablenav($which)
    // {
    //     if ($which == "top") {
    //        echo '';
    //     }
    //     if ($which == "bottom") {
    //         echo 'this is the below table';
    //     }
    // }

        //looping through data
    public function column_default($item, $column_name)
    {
        
        switch ($column_name) {
            
            case 'id':
                return $item[$column_name];
                break;
            case 'sc_name':
                return $item[$column_name];
                break;
            case 'shortcode':
                return '['.$item[$column_name].']';
                break;
            case 'content':
                return $item[$column_name];
                break;
            case 'short_desc':
                return $item[$column_name];
                break;
            default:
                echo "No data found";
        }
        

    }

    //For actions button on Name column
    function column_sc_name($item){

        $action =  array(
            'edit' => sprintf('<a href="?page=%s&action=%s&sc_id=%s">Edit</a>',$_GET['page'],'sc_edit',$item['id']),
            'delete' => sprintf('<a href="?page=%s&action=%s&sc_id=%s">Delete</a>', $_GET['page'], 'sc_delete', $item['id'])
        );

        return sprintf('%1$s %2$s',$item['sc_name'],$this->row_actions($action));
        
    }

    

   

}
//Displaying data from database
function sc_content_show_list_data(){
    $sc_content_table  = new Sc_content_table();
     
    $sc_content_table->prepare_items();
   
   ?>
   <div class="wrap">
        <h2>All Shortcodes <a href="admin.php?page=add_new" class="page-title-action">Add New</a></h2><br />
        <form method="post" name="form_search_shortcode" action="<?php $_SERVER['PHP_SELF']. '?page=sc_content'?>">
            <?php $sc_content_table->search_box('Seach Shortcode(s)','sc_shortcode_search');?>
        </form>    
        <?php $sc_content_table->display(); ?>
   </div>
   <?php
    
}

sc_content_show_list_data();
