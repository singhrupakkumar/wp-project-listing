<?php

namespace Citadela\Pro\Comments_Extension;

class Feature {

	function __construct() {
		if ( ! is_admin() and $this->settings()->value('show') ) {
			add_filter( 'comment_form_defaults', [ $this, 'intercept_comment_form_defaults' ] );
			add_filter( 'comment_text', [ $this, 'comment_text' ], 10, 2 );
			add_filter( 'get_comment_author_link', [ $this, 'get_comment_author_link' ] );
		}

		
	}



	function settings() {
		static $settings = null;
		if ( ! $settings ) {
			$settings = new Settings;
		}
		return $settings;
	}



	function comment_text( $comment_text, $comment ) {
		// remove hyperlinks from comment text
		if( is_singular('post') ){
			// check if comment is from quest or registered user
			if( intval( $comment->user_id ) === 0 ){
				//guest
				if( $this->settings()->value('comment_disable_links_from_guest') ){
					$comment_text = preg_replace("/<\/?a( [^>]*)?>/i", "", $comment_text);
				}
			}else{
				// registered user
				if( user_can( $comment->user_id, 'edit_posts' ) ){
					// roles Contributor and higher
					if( $this->settings()->value('comment_disable_links_from_editor') ){
						$comment_text = preg_replace("/<\/?a( [^>]*)?>/i", "", $comment_text);	
					}
				}else{
					// Subscriber
					if( $this->settings()->value('comment_disable_links_from_noneditor') ){
						$comment_text = preg_replace("/<\/?a( [^>]*)?>/i", "", $comment_text);	
					}
				}
			}
		} 
		return $comment_text;
	}
			


	function get_comment_author_link( $return ) {
		// remove hyperlink from author name
		if( is_singular('post') and $this->settings()->value('website_disable_links') ){
			$return = preg_replace("/<\/?a( [^>]*)?>/i", "", $return);
		}
		return $return;
	}



	function intercept_comment_form_defaults( $defaults ) {
		$commenter = wp_get_current_commenter();
		$req       = get_option( 'require_name_email' );
		$req_attrs = $req ? ' aria-required="true" required="required" ' : '';
		$req_html  = $req ? ' <span class="required">*</span>' : '';
		$help_text = function( $field ) {
			return $this->settings()->value("{$field}_help") ? '<div class="citadela-comments-extension-help">'.$this->settings()->value("{$field}_help").'</div>' : '';
		};

		if ( isset( $defaults['fields']['author'] ) ) {
			$defaults['fields']['author'] = sprintf(

				'<p class="comment-form-author">
					<label for="author">%1$s%2$s</label>
					<input id="author" name="author" type="text" value="%3$s" size="30" maxlength="245" %4$s />
				</p> %5$s',

				$this->settings()->value('name_label') ?: __( 'Name' ),
				$req_html,
				esc_attr( $commenter['comment_author'] ),
				$req_attrs,
				$help_text( 'name' )
			);
		}

		if ( isset( $defaults['fields']['email'] ) ) {
			$defaults['fields']['email'] = sprintf(

				'<p class="comment-form-email">
					<label for="email">%1$s%2$s</label>
					<input id="email" name="email" type="email" value="%3$s" size="30" maxlength="100" aria-describedby="email-notes" %4$s />
				</p> %5$s',

				$this->settings()->value('email_label') ?: __( 'Email' ),
				$req_html,
				esc_attr( $commenter['comment_author_email'] ),
				$req_attrs,
				$help_text( 'email' )
			);
		}

		if ( isset( $defaults['fields']['url'] ) ) {
			$defaults['fields']['url'] = sprintf(

				'<p class="comment-form-url">
					<label for="url">%1$s</label> ' .
					'<input id="url" name="url" type="url" value="%2$s" size="30" maxlength="200" />
				</p> %3$s',

				$this->settings()->value('website_label') ?: __( 'Website' ),
				esc_attr( $commenter['comment_author_url'] ),
				$help_text( 'website' )
			);
		}

		if ( isset( $defaults['comment_field'] ) ) {
			$defaults['comment_field'] = sprintf(
				'<p class="comment-form-comment">
					<label for="comment">%1$s</label>
					<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea>
				</p> %2$s',

				$this->settings()->value('comment_label') ?: _x( 'Comment', 'noun' ),
				$help_text( 'comment' )
			);
		}

		$defaults['class_form'] .= ' citadela-comments-extension-form';

		return $defaults;
	}
}
