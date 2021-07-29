<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Zoho_Flow_WordPress_org extends Zoho_Flow_Service
{
	public function init(){
		require_once __DIR__ . '/webhook-processor.php';
	}
	public function addActionHooks(){
	}
	public function initRestApis(){
	}
	
	public function get_posts( $request ){
	    $data = array();
	    
	    $args = array("posts_per_page" => 10, "orderby" => "comment_count");
	    $posts_array = get_posts($args);
	    $schema = $this->get_post_schema( $request );
	    foreach($posts_array as $post)
	    {
	        if( isset( $schema['properties']['post_id'])){
	            $post_data['post_id'] = $post->ID;
	        }
	        if ( isset( $schema['properties']['post_title'] ) ) {
	           $post_data['post_title'] = $post->post_title;
	        }
	        if ( isset( $schema['properties']['post_content'] ) ) {
	           $post_data['post_content'] = $post->post_content;
	        }
	        if ( isset( $schema['properties']['post_date'] ) ) {
	           $post_data['post_date'] = $post->post_date;
	        }
	        if ( isset( $schema['properties']['post_status'] ) ) {
	           $post_data['post_status'] = $post->post_status;
	        }
	        if ( isset( $schema['properties']['comment_count'] ) ) {
	           $post_data['comment_count'] = $post->comment_count;
	        }
	        array_push($data, $post_data);
	    }
	    return rest_ensure_response( $data);
	}
	
	public function get_user_by( $request ){
	    error_log('get_user_by called');
	    
	    $login = esc_attr($request['login']);
	    if(isset($login) && filter_var($request['login'], FILTER_VALIDATE_EMAIL)){
	        $user = get_user_by('email', $login);
	    }
	    else if(isset($request['user_id'])){
	        $user_id = $request['user_id'];
	        if(!ctype_digit($user_id)){
	            return new WP_Error( 'rest_bad_request', esc_html__( 'The User ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
	        }
	        $user = get_user_by('id', $user_id);
	    }
	    else if (isset($request['login'])) {
	        $user = get_user_by('login', $request['login']);
	    }

	    return rest_ensure_response($user);
	}
	
	public function get_users( $request ){
	    $data = array();
	    $users = get_users();
	    $schema = $this->get_user_schema();

	    foreach($users as $user){
	        if( isset( $schema['properties']['user_id'])){
	            $post_data['user_id'] = $user->ID;
	        }
	        if( isset( $schema['properties']['user_login'])){
	           $post_data['user_login'] = $user->user_login;
	        }
	        if( isset( $schema['properties']['user_email'])){
	            $post_data['user_email'] = $user->user_email;
	        }
	        if( isset( $schema['properties']['user_registered'])){
	            $post_data['user_registered'] = $user->user_registered;
	        }
	        if( isset( $schema['properties']['display_name'])){
	            $post_data['display_name'] = $user->display_name;
	        }
	        if( isset( $schema['properties']['role'])){
	           $post_data['role'] = $user->caps;
	        }
	        if( isset( $schema['properties']['roles'])){
	           $post_data['roles'] = $user->allcaps;
	        }
	        array_push($data, $post_data);
	    }
	    return rest_ensure_response($data);
	}
	
	public function get_comments( $request ){
	    $data = array();
            $args = array('');
	        $comments = get_comments($args);
	        
	        $schema = $this->get_comment_schema();
	        foreach ($comments as $comment){
	            if( isset( $schema['properties']['comment_id'])){
	               $post_data['comment_id'] =$comment->comment_ID;
	            }
	            if( isset( $schema['properties']['comment_post_id'])){
	                $post_data['comment_post_id']=$comment->comment_post_ID;
	            }
	            if(isset($schema['properties']['comment_author'])){
	                $post_data['comment_author'] =$comment->comment_author;
	            }
	            if(isset($schema['properties']['comment_author_email'])){
	                $post_data['comment_author_email'] = $comment->comment_author_email;
	            }
	            if(isset($schema['properties']['comment_content'])){
	                $post_data['comment_content'] = $comment->comment_content;
	            }
	            if(isset($schema['properties']['comment_date'])){
	                $post_data['comment_date'] = $comment->comment_date;
	            }
	            array_push($data, $post_data);
	        }
	    return rest_ensure_response($data);
	}
	
	public function create_post($request){
	    error_log('create post');
	    $postarr = array(
	        'post_title'   =>  wp_strip_all_tags($request['post_title']),
	        'post_content' =>  wp_strip_all_tags($request['post_content']),
	        'post_status'  =>  $request['post_status'],
	        'post_author'  =>  get_current_user_id(),
	        'post_type'    =>  'post',
	        'post_date'    =>  date( 'Y-m-d H:i:s', time() ),
	        'comment_status'   =>  $request['comment_status'],
	        'ping_status'  =>  $request['ping_status']
	    );
	    
	    $post_id = wp_insert_post( $postarr);
	    if(is_wp_error($post_id)){
	        //the post is valid
	        $errors = $post_id->get_error_messages();
	        $error_code = $post_id->get_error_code();
	        foreach ($errors as $error) {
	            return new WP_Error( $error_code, esc_html__( $error, 'zoho-flow' ), array('status' => 400) );
	        }
	    }
	    $request['post_id'] = $post_id;
	    $this->call_webhook_for_post($post_id, $postarr['post_type']);
	    
	    return rest_ensure_response(get_post($post_id));
	    
	}
	
	public function update_post($request){
	    error_log('update post');
	    $post_id = $request['post_id'];
	    if(!ctype_digit($post_id)){
	        return new WP_Error( 'rest_bad_request', esc_html__( 'The post ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
	    }
	    
	    $post_arr = array(
	        'ID'           =>  $post_id,
	        'post_title'   =>  wp_strip_all_tags($request['post_title']),
	        'post_content' =>  wp_strip_all_tags($request['post_content']),
	        'post_status'  =>  $request['post_status'],
	        'post_author'  =>  $request['post_author'],
	        'post_type'    =>  'post',
	        'post_date'    =>  date( 'Y-m-d H:i:s', time() )
	    );
	    $post_id = wp_update_post($post_arr);
	    if (is_wp_error($post_id)) {
	        $errors = $post_id->get_error_messages();
	        $error_code = $post_id->get_error_code();
	        foreach ($errors as $error) {
	            return new WP_Error( $error_code, esc_html__( $error, 'zoho-flow' ) , array('status' => 400));
	        }
	    }
	    $this->call_webhook_for_post($post_id, $post_arr['post_type']);
	    
	    return rest_ensure_response(get_post($post_id));
	}
	
	public function create_user($request){
	    error_log('create user');
	    $userdata = array(
	        'user_login'   =>  $request['user_login'],
	        'user_pass'    =>  $request['user_pass'],
	        'user_email'   =>  $request['user_email'],
	        'last_name'    =>  $request['last_name'],
	        'first_name'   =>  $request['first_name'],
	        'user_registered'  =>  date( 'Y-m-d H:i:s', time()),
	        'role'         =>  $request['role'],
	        'user_url'     =>  $request['user_url'],
	        'description'  =>  $request['description']
	    );
	    $user_id = wp_insert_user( $userdata ) ;
	    	    
	    if ( is_wp_error( $user_id ) ) {
	        $errors = $user_id->get_error_messages();
	        $error_code = $user_id->get_error_code();
	        foreach ($errors as $error) {
	            return new WP_Error($error_code, esc_html__( $error, 'zoho-flow' ), array( 'status' => 400 )  );
	        }
	    }
	    return rest_ensure_response(get_user_by('ID', $user_id));
	}
	
	public function update_user($request){
	    $user_id = $request['user_id'];
	    if(!ctype_digit($user_id)){
	        return new WP_Error( 'rest_bad_request', esc_html__( 'The user ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
	    }
	    $olddata = get_user_by('ID', $user_id);
	    
	    $userdata = array(
	        'ID'           => $request['user_id'],
	        'user_login'   =>  (isset($request['user_login']) && !empty($request['user_login'])) ? $request['user_login'] : $olddata->user_login,
	        'user_pass'    =>  (isset($request['user_pass']) && !empty($request['user_pass'])) ? $request['user_pass'] : $olddata->user_pass,
	        'user_email'   =>  (isset($request['user_email']) && !empty($request['user_email'])) ? $request['user_email'] : $olddata->user_email,
	        'last_name'    =>  (isset($request['last_name']) && !empty($request['last_name'])) ? $request['last_name'] : $olddata->last_name,
	        'first_name'   =>  (isset($request['first_name']) && !empty($request['first_name'])) ? $request['first_name'] : $olddata->first_name,
	        'user_registered'  =>  date( 'Y-m-d H:i:s', time()),
	        'role'         =>  (isset($request['role']) && !empty($request['role'])) ? $request['role'] : $olddata->role,
	        'user_url'     =>  (isset($request['user_url']) && !empty($request['user_url'])) ? $request['user_url'] : $olddata->user_url,
	        'description'  =>  (isset($request['description']) && !empty($request['description'])) ? $request['description'] : $olddata->description,
	    );
	    $data = wp_update_user( $userdata ) ;
	    if ( is_wp_error( $data ) ) {
	        $errors = $data->get_error_messages();
	        foreach ($errors as $error) {
	            return new WP_Error( 'rest_bad_request', esc_html__( $error, 'zoho-flow' ), array('status' => 400) );
	        }
	    }
	    
	    return rest_ensure_response(get_user_by('ID', $user_id));
	}
	
	public function call_webhook_for_post($post_id, $post_type){
	    error_log('execute webhook for post');
	    $data = get_post($post_id);
	    $args = array(
	        'post_type'    =>  'posts'
	    );
	    $webhooks = $this->get_webhook_posts($args);
	    foreach ( $webhooks as $webhook ) {
	        $url = $webhook->url;
	        zoho_flow_execute_webhook($url, $data, array());
	    }
	}
	
	public function get_webhooks($request){
	    error_log('get_webhooks');
	    $data = array();
	    $post_id = $request['post_id'];
	    if(!ctype_digit($post_id)){
	        return new WP_Error( 'rest_bad_request', esc_html__( 'The post ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
	    }
	    $args = array(
	        'post_id' => $post_id
	    );
	    $webhooks = $this->get_webhook_posts($args);
	    
	    foreach ( $webhooks as $webhook ) {
	        $webhook = array(
	            'plugin_service' => $this->get_service_name(),
	            'id' => $webhook->ID,
	            'form_id' => $post_id,
	            'url' => $webhook->url
	        );
	        array_push($data, $webhook);
	    }
	    
	    return rest_ensure_response( $data );
	}
	
	public function create_post_comments_webhook($request){
	    $postid = $request['post_id'];
	    $url = esc_url_raw($request['url']);
	    if(!ctype_digit($postid)){
	        return new WP_Error( 'rest_bad_request', esc_html__( 'The post ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
	    }
	    $post_obj = get_post($postid);
	    if(empty($post_obj)){
	        return new WP_Error( 'rest_not_found', esc_html__( 'The post is not found.', 'zoho-flow' ), array( 'status' => 404 ) );
	    }
	    $post_title = $post_obj->post_title;
	    
	    $post_id = $this->create_webhook_post($post_title, array(
	        'post_id' => $postid,
	        'url' => $url
	    ));
	    return rest_ensure_response( array(
	        'plugin_service' => $this->get_service_name(),
	        'id' => $post_id,
	        'post_id' =>$postid,
	        'url' => $url
	    ) );
	}
	
	public function delete_webhook($request){
	    $webhook_id = $request['webhook_id'];
	    if(!ctype_digit($webhook_id)){
	        return new WP_Error( 'rest_bad_request', esc_html__( 'The post ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
	    }
	    $result = $this->delete_webhook_post($webhook_id);
	    if(is_wp_error($result)){
	        return $result;
	    }
	    return rest_ensure_response(array(
	        'plugin_service' => $this->get_service_name(),
	        'id' => $result->ID
	    ));
	    return rest_ensure_response($result);
	}
	
	public function get_webhooks_for_post( $request ){
	    
	    $data = array();
	    $args = array(
	        'post_type' => $request['post_type']
	    );
	    error_log('args ');
	    error_log(print_r($args, true));
	    $webhooks = $this->get_webhook_posts($args);
	    error_log('webhooks');
	    error_log(print_r($webhooks, true));
	    foreach ( $webhooks as $webhook ) {
	        $webhook = array(
	            'plugin_service' => $this->get_service_name(),
	            'id' => $webhook->ID,
	            'post_type' => $request['post_type'],
	            'url' => $webhook->url
	        );
	        array_push($data, $webhook);
	    }
	    
	    return rest_ensure_response( $data );
	}
	
	public function create_webhook_for_post( $request ){
	    $post_title = $request['post_type'];
	    $url = esc_url_raw($request['url']);
	    $post_id = $this->create_webhook_post($post_title, array(
	        'post_type' => $request['post_type'],
	        'url' => $url
	    ));
	    
	    return rest_ensure_response( array(
	        'plugin_service' => $this->get_service_name(),
	        'id' => $post_id,
	        'post_type' =>$request['post_type'],
	        'url' => $url
	    ) );
	}
	
	public function get_comments_webhooks( $request ){
	    
	    $data = array();
	    $args = array(
	        'post_type' => 'comments'
	    );
	    $webhooks = $this->get_webhook_posts($args);
	    
	    foreach ( $webhooks as $webhook ) {
	        $webhook = array(
	            'plugin_service' => $this->get_service_name(),
	            'id' => $webhook->ID,
	            'url' => $webhook->url
	        );
	        array_push($data, $webhook);
	    }
	    
	    return rest_ensure_response( $data );
	}
	
	public function create_comments_webhooks( $request ) {
	    $post_type ='comments';
	    $url = esc_url_raw($request['url']);
	    $post_id = $this->create_webhook_post($post_type, array(
	    	'post_type' => $$post_type,
	        'url' => $url
	    ));
	    
	    return rest_ensure_response( array(
	        'plugin_service' => $this->get_service_name(),
	        'id' => $post_id,
	        'post_type' =>$request['post_type'],
	        'url' => $url
	    ) );
	}
	
	public function wp_core_register_post_type(){
	    error_log('in register post_type');
	    if(empty(post_type_exists('users'))){
    	    $args = array(
    	        'public'    => true,
    	        'label'     => __( 'Users', 'textdomain' ),
    	        'capability_type' => 'users'
    	    );
    	    register_post_type( 'users', $args );
	    }
	}
	
	public function process_comment_post($comment_id,  $commentdata_comment_approved, $commentdata){
	    error_log('current action is process_comment_post');

	    $args = array(
	        'post_id' => $commentdata['comment_post_ID']
	    );
	    $commentdata['comment_id'] = $comment_id;
	    
	    $webhooks = $this->get_webhook_posts($args);
	    if(empty($webhooks)){
	        $args_array = array(
	            'post_type' => 'comments'
	        );
	        $webhooks = $this->get_webhook_posts($args_array);
	    }
	    foreach ( $webhooks as $webhook ) {
	        $url = $webhook->url;
	        zoho_flow_execute_webhook($url, $commentdata, array());
	    }
	}
	
	public function process_spammed_comment($comment_id, $comment){
	    error_log("current action is process_spammed_comment");
	    
	    $args = array(
	        'post_id' => $comment->comment_post_ID
	    );
	    
	    $webhooks = $this->get_webhook_posts($args);
	    foreach ( $webhooks as $webhook ) {
	        $url = $webhook->url;
	        zoho_flow_execute_webhook($url, $comment, array());
	    }
	}
	
	public function process_edit_comment($comment_ID, $comment){
	    error_log("current action is process_edit_comment");
	    
	    $comment['commment_id'] = $comment_ID;
	    $args = array(
	        'post_id'=> $comment['comment_post_ID']
	    );
	    
	    $webhooks = $this->get_webhook_posts($args);
	    foreach ( $webhooks as $webhook ) {
	        $url = $webhook->url;
	        zoho_flow_execute_webhook($url, $comment, array());
	    }
	}
	
	public function process_set_comment_status($comment_id, $comment_status){
	    error_log("current action is process_set_comment_status");
	    
	    $comment = get_comment($comment_id);
	    $comment->comment_status = $comment_status;
	    $args = array(
	        'post_id'=> $comment->comment_post_ID
	    );
	    
	    $webhooks = $this->get_webhook_posts($args);
	    foreach ( $webhooks as $webhook ) {
	        $url = $webhook->url;
	        zoho_flow_execute_webhook($url, $comment, array());
	    }
	}
	
	public function process_user_register($user_id){
	    $user = get_user_by('ID', $user_id);
	    error_log('user registered');
	    $args = array(
	        'post_type'=> 'users'
	    );
	    
	    $webhooks = $this->get_webhook_posts($args);
	    foreach ( $webhooks as $webhook ) {
	        $url = $webhook->url;
	        zoho_flow_execute_webhook($url, $user, array());
	    }
	}

	public function process_profile_update($user_id, $old_user_data){
	    error_log('profile update');
	    $user = get_user_by('ID', $user_id);
	    $times = did_action('profile_update');
	    if($times ===1){
	    	    $args = array(
	    	        'post_type'=> 'users' 
	    	    );
	    	    
	    	    $webhooks = $this->get_webhook_posts($args);
	    	    foreach ( $webhooks as $webhook ) {
	    	        $url = $webhook->url;
	    	        zoho_flow_execute_webhook($url, $user, array());
	    	    }
	    	    return rest_ensure_response($user);
	    }
	}
	
	public function process_save_post($post_id, $post , $update){
	    
	    error_log('save post actoin');
	    $post_status = $post->post_status;
	    $post_type = $post->post_type;
	    if (wp_is_post_revision($post_id)) {
	        return;
	    }
	    $times = did_action('save_post');
	    if($times === 1 && $post_type ==='post' && $post_status==='publish'){
    		$args = array(
    		    'post_type' => 'posts'
    		);
    		$webhooks = $this->get_webhook_posts($args);
    		foreach($webhooks as $webhook){
    		   $url = $webhook->url;
    		   zoho_flow_execute_webhook($url, $post, array());
    		}
	    }
	}
	
	public function get_post_schema() {
	    $schema = array(
	        '$schema'              => 'http://json-schema.org/draft-04/schema#',
	        'title'                => 'posts',
	        'type'                 => 'post',
	        'properties'           => array(
	            'post_id' => array(
	                'description'  => esc_html__( 'Post Id', 'zoho-flow' ),
	                'type'         => 'integer',
	                'context'      => array('view'),
	            ),
	            'post_title' => array(
	                'description'  => esc_html__( 'Post Title', 'zoho-flow' ),
	                'type'         => 'string',
	                'context'      => array( 'view', 'edit'),
	                'readonly'     => true,
	            ),
	            'post_content' => array(
	                'description'  => esc_html__( 'Content of a Post', 'zoho-flow' ),
	                'type'         => 'string',
	                'context'      => array( 'view', 'edit'),
	            ),
	            'post_date' => array(
	                'description' => esc_html__("Created Date of Post", "zoho-flow"),
	                'type'        => 'date',
	                'context'     => array('view'),
	                'readonly'    => true,
	            ),
	            'post_status' => array(
	                'description' => esc_html__( 'Post status', 'zoho-flow' ),
	                'type'        => 'string',
	                'context'     => array('view'),
	            ),
	            'comment_count' => array(
	                'description' => esc_html__('Comment count', 'zoho-flow'),
	                'type'        => 'integer',
	                'context'     => array('view'),
	            ),
	        ),
	    );
	    
	    return $schema;
	}
	
	public function get_user_schema() {
	    $schema = array(
	        '$schema'              => 'http://json-schema.org/draft-04/schema#',
	        'title'                => 'users',
	        'type'                 => 'user',
	        'properties'           => array(
	            'user_id' => array(
	                'description'  => esc_html__( 'User Id', 'zoho-flow' ),
	                'type'         => 'integer',
	                'context'      => array('view'),
	            ),
	            'user_login' => array(
	                'description'  => esc_html__( 'User login', 'zoho-flow' ),
	                'type'         => 'string',
	                'context'      => array( 'view', 'edit'),
	                'readonly'     => true,
	            ),
	            'user_email' => array(
	                'description'  => esc_html__( 'User email', 'zoho-flow' ),
	                'type'         => 'string',
	                'context'      => array( 'view', 'edit'),
	            ),
	            'user_registered' => array(
	                'description' => esc_html__("User registered date", "zoho-flow"),
	                'type'        => 'date',
	                'context'     => array('view'),
	                'readonly'    => true,
	            ),
	            'display_name' => array(
	                'description' => esc_html__( 'Display Name', 'zoho-flow' ),
	                'type'        => 'string',
	                'context'     => array('view'),
	            ),
	            'role' => array(
	                'description' => esc_html__('Comment count', 'zoho-flow'),
	                'type'        => 'array',
	                'context'     => array('view'),
	            ),
	            'roles' => array(
	                'description' => esc_html__('User role', 'zoho-flow'),
	                'type'        => 'array',
	                'context'     => array('view'),
	            ),
	        ),
	    );
	    
	    return $schema;
	}

	public function get_comment_schema() {
	    $schema = array(
	        '$schema'              => 'http://json-schema.org/draft-04/schema#',
	        'title'                => 'posts',
	        'type'                 => 'post',
	        'properties'           => array(
	            'comment_id' => array(
	                'description'  => esc_html__( 'Comment Id', 'zoho-flow' ),
	                'type'         => 'integer',
	                'context'      => array('view'),
	            ),
	            'comment_post_id' => array(
	                'description'  => esc_html__( 'Comment Post Id', 'zoho-flow' ),
	                'type'         => 'integer',
	                'context'      => array( 'view'),
	                'readonly'     => true,
	            ),
	            'comment_author' => array(
	                'description'  => esc_html__( 'Author of the comment', 'zoho-flow' ),
	                'type'         => 'string',
	                'context'      => array( 'view', 'edit'),
	            ),
	            'comment_author_email' => array(
	                'description' => esc_html__("Email of the comment author", "zoho-flow"),
	                'type'        => 'string',
	                'context'     => array('view'),
	                'readonly'    => true,
	            ),
	            'comment_content' => array(
	                'description' => esc_html__( 'Comment content', 'zoho-flow' ),
	                'type'        => 'string',
	                'context'     => array('view', 'edit'),
	            ),
	            'comment_date' => array(
	                'description' => esc_html__('Commented date', 'zoho-flow'),
	                'type'        => 'date',
	                'context'     => array('view'),
	            ),
	        ),
	    );
	    
	    return $schema;
	}
}
