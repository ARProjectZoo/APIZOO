<?php
use \Model\Users;
use Firebase\JWT\JWT;

class Controller_Stories extends Controller_Base
{
    private function newStory($input)
    {
    		$story = new Model_Stories();
            $story->photo = $input['photo'];
            $story->comment = $input['comment'];
            $story->id_user = $this->idUser;
            return $story;
    }

    private function saveStory($story)
    {
    	$storyExists = Model_Users::find('all', 
    								array('where' => array(
    													array('id_story', '=', $story->id_story)
    														)
    									)
    							);
    	if(empty($storyExists)){

    		$storyToSave = $story;
    		$storyToSave->save();
    		$json = $this->response(array(
                    'code' => 201,
                    'message' => 'Historia creada',
                    'name' => $story->story //
                ));
    		return $json;
    	}else{
    		$json = $this->response(array(
                    'code' => 204,
                    'message' => 'Historia ya existe'
                ));
    		return $json;
    	}
    }
     private function showStory($story)
    {/*
    	$storyExists = Model_Users::find('all', 
    								array('where' => array(
    													array('id_story', '=', $story->id_story)
    														)
    									)
    							);
    	if(empty($storyExists)){

    		$storyToSave = $story;
    		$storyToSave->save();
    		$json = $this->response(array(
                    'code' => 201,
                    'message' => 'Historia creada',
                    'name' => $story->story //
                ));
    		return $json;
    	}else{
    		$json = $this->response(array(
                    'code' => 204,
                    'message' => 'Historia ya existe'
                ));
    		return $json;
    	}*/
    }

    public function post_create()
    {
        try {
            if ( !isset($_POST['photo']) || !isset($_POST['comment'])) 
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'Algun paramentro esta vacio'
                ));
                return $json;
            }if(!empty($_POST['photo']) && !empty($_POST['comment'])){
				$input = $_POST;
	            $newStory = $this->newStory($input);
	           	$json = $this->saveStory($newStory);
	            return $json;
	        }else{
	        	$json = $this->response(array(
                    'code' => 400,
                    'message' => 'Algun campo vacio'
                ));
                return $json;
	        }
        }catch (Exception $e){
            $json = $this->response(array(
                'code' => 500,
                'message' =>  $e->getMessage()
            ));
            return $json;
        }      
    }
	
	public function post_modifyStory()
	{
		try{
			$input = $_POST;
			if ( !isset($_POST['photo']) || !isset($_POST['comment']) ) {
	            $json = $this->response(array(
	                    'code' => 400,
	                    'message' => 'alguno de los datos esta vacio'
	                ));
	                return $json;
	        }else if( !empty($_POST['photo']) && !empty($_POST['comment'])){
		    	$story = Model_Stories::find('all', 
		           					array('where' => array(
		           							array('photo', '=', $input['photo']), 
		           							array('comment', '=', $input['comment'])
		           							)
		           						)
		           					);
		    if($story != null){
		    	$story = reset($story);
		    	$photo = $story->photo;
	            $comment = $story->comment;
	            $id = $story->id;
	            $id_user = $story->id_user;
		    	$token = $this->encodeToken($photo, $comment, $id, $id_user);
		        $json = $this->response(array(
		                    'code' => 200,
		                    'message' => 'Historia encontrada, se puede cambiar',
		                    'token' => $token
		                    ));
		                return $json;
		    }else{
		    	 $json = $this->response(array(
		                    'code' => 400,
		                    'message' => 'Historia no encontrada.',
		                    'data' => $token
		                    ));
		                return $json;
		    	}
			}
		}catch(Exception $e){
		    		 $json = $this->response(array(
		                'code' => 500,
		                'message' =>  $e->getMessage()
		            ));
		            return $json;
		    	}
	}

	public function post_modify()
	{
		$newPhoto = $_POST['newPhoto'];
		if( isset($newPhoto)) {
			$decodeToken = $this->decodeToken();
			$story = Model_stories::find('all', 
			            					array('where' => array(
			            							array('id_story', '=', $decodeToken->id_story), 
			            							array('photo', '=', $decodeToken->photo)
			            							)
			            						)
			            					);
			if(isset($newPhoto)){
				$story = reset($story);
				$query = DB::update($story);
				$query -> value('photo', $newPhoto);
				$query -> execute();
				$json = $this->response(array(
			                    'code' => 200,
			                    'message' => 'Foto modificada correctamente',
			                    'token' => $token
			                    ));
			                return $json;
			}else{
				$json = $this->response(array(
			                    'code' => 400,
			                    'message' => 'Campos vacios',
			                    'data' => ""
			                    ));
			                return $json;
			}
		}else{
			$json = $this->response(array(
			                    'code' => 400,
			                    'message' => 'Foto vacia, por favor rellenela',
			                    'data' => ""
			                    ));
			                return $json;
		}
		$newComment = $_POST['newComment'];
		if( isset($newComment)) {
			$decodeToken = $this->decodeToken();
			$story = Model_stories::find('all', 
			            					array('where' => array(
			            							array('id_story', '=', $decodeToken->id_story), 
			            							array('comment', '=', $decodeToken->comment)
			            							)
			            						)
			            					);
			if(isset($newComment)){
				$story = reset($story);
				$query = DB::update($story);
				$query -> value('comment', $newComment);
				$query -> execute();
				$json = $this->response(array(
			                    'code' => 200,
			                    'message' => 'Comentario modificado correctamente',
			                    'token' => $token
			                    ));
			                return $json;
			}else{
				$json = $this->response(array(
			                    'code' => 400,
			                    'message' => 'Campos vacios',
			                    'data' => ""
			                    ));
			                return $json;
			}
		}else{
			$json = $this->response(array(
			                    'code' => 400,
			                    'message' => 'Comentario vacio, por favor rellenelo',
			                    'data' => ""
			                    ));
			                return $json;
		}

	}
}
