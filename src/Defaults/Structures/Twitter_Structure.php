<?php
/**
 * Twitter_Structure class
 *
 * @package APIAPIDefaults
 * @subpackage Structures
 * @since 1.0.0
 */

namespace APIAPI\Defaults\Structures;

use APIAPI\Core\Structures\Structure;

if ( ! class_exists( 'APIAPI\Defaults\Structures\Twitter_Structure' ) ) {

	/**
	 * Structure class for the Twitter API.
	 *
	 * @since 1.0.0
	 */
	class Twitter_Structure extends Structure {
		/**
		 * Sets up the API structure.
		 *
		 * This method should populate the routes array, and can also be used to
		 * handle further initialization functionality, like setting the authenticator
		 * class and default authentication data.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function setup() {
			$this->base_uri = 'https://api.twitter.com/1.1';

			$this->authenticator = 'twitter-oauth1';
			$this->authentication_data_defaults = array(
				'request'   => 'https://api.twitter.com/oauth/request_token',
				'authorize' => 'https://api.twitter.com/oauth/authorize',
				'access'    => 'https://api.twitter.com/oauth/access_token',
			);

			$this->routes['/account/remove_profile_banner.json'] = array(
				'methods' => array(
					'POST' => array(
						'description'          => 'Removes the uploaded profile banner for the authenticating user. Returns HTTP 200 upon success.',
						'needs_authentication' => true,
						'params'               => array(),
					),
				),
			);

			$this->routes['/account/settings.json'] = array(
				'methods' => array(
					'GET'  => array(
						'description'          => 'Returns settings (including current trend, geo and sleep time information) for the authenticating user.',
						'needs_authentication' => true,
						'params'               => array(),
					),
					'POST' => array(
						'description'          => 'Updates the authenticating user’s settings.',
						'needs_authentication' => true,
						'params'               => array(
							'sleep_time_enabled'  => array(
								'description' => 'When set to true, t or 1 , will enable sleep time for the user.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'start_sleep_time'    => array(
								'description' => 'The hour that sleep time should begin if it is enabled (i.e. 00-23).',
								'type'        => 'integer',
								'minimum'     => 0,
								'maximum'     => 23,
							),
							'end_sleep_time'       => array(
								'description' => 'The hour that sleep time should end if it is enabled (i.e. 00-23).',
								'type'        => 'integer',
								'minimum'     => 0,
								'maximum'     => 23,
							),
							'time_zone'            => array(
								'description' => 'The timezone dates and times should be displayed in for the user. The timezone must be one of the Rails TimeZone names.',
								'type'        => 'string',
							),
							'trend_location_woeid' => array(
								'description' => 'The Yahoo! Where On Earth ID to use as the user’s default trend location. Global information is available by using 1 as the WOEID.',
								'type'        => 'integer',
							),
							'lang'                 => array(
								'description' => 'The language which Twitter should render in for this user. The language must be specified by the appropriate two letter ISO 639-1 representation.',
								'type'        => 'string',
							),
						),
					),
				),
			);

			$this->routes['/account/update_profile.json'] = array(
				'methods' => array(
					'POST' => array(
						'description'          => 'Sets some values that users are able to set under the “Account” tab of their settings page. Only the parameters specified will be updated.',
						'needs_authentication' => true,
						'params'               => array(
							'name'               => array(
								'description' => 'Full name associated with the profile. Maximum of 20 characters.',
								'type'        => 'string',
							),
							'url'                => array(
								'description' => 'URL associated with the profile. Maximum of 100 characters.',
								'type'        => 'string',
							),
							'location'           => array(
								'description' => 'The city or country describing where the user of the account is located. Maximum of 30 characters.',
								'type'        => 'string',
							),
							'description'        => array(
								'description' => 'A description of the user owning the account. Maximum of 160 characters.',
								'type'        => 'string',
							),
							'profile_link_color' => array(
								'description' => 'Sets a hex value that controls the color scheme of links used on the authenticating user’s profile page on twitter.com. This must be a valid hexadecimal value, and may be either three or six characters (ex: F00 or FF0000).',
								'type'        => 'string',
							),
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'skip_status'      => array(
								'description' => 'When set to either true, t or 1 statuses will not be included in the returned user object.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
						),
					),
				),
			);

			$this->routes['/account/update_profile_background_image.json'] = array(
				'methods' => array(
					'POST' => array(
						'description'          => 'Updates the authenticating user’s profile background image. This method can also be used to enable or disable the profile background image.',
						'needs_authentication' => true,
						'params'               => array(
							'image'            => array(
								'description' => 'The background image for the profile, base64-encoded. Must be a valid GIF, JPG, or PNG image of less than 800 kilobytes in size.',
								'type'        => 'string',
							),
							'tile'             => array(
								'description' => 'Whether or not to tile the background image. If set to true, t or 1 the background image will be displayed tiled.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'skip_status'      => array(
								'description' => 'When set to either true, t or 1 statuses will not be included in the returned user object.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'media_id'         => array(
								'description' => 'Specify the media to use as the background image.',
								'type'        => 'integer',
							),
						),
					),
				),
			);

			$this->routes['/account/update_profile_banner.json'] = array(
				'methods' => array(
					'POST' => array(
						'description'          => 'Uploads a profile banner on behalf of the authenticating user.',
						'needs_authentication' => true,
						'params'               => array(
							'banner'      => array(
								'required'    => true,
								'description' => 'The Base64-encoded or raw image data being uploaded as the user’s new profile banner.',
								'type'        => 'string',
							),
							'width'       => array(
								'description' => 'The width of the preferred section of the image being uploaded in pixels.',
								'type'        => 'integer',
							),
							'height'      => array(
								'description' => 'The height of the preferred section of the image being uploaded in pixels.',
								'type'        => 'integer',
							),
							'offset_left' => array(
								'description' => 'The number of pixels by which to offset the uploaded image from the left.',
								'type'        => 'integer',
							),
							'offset_top'  => array(
								'description' => 'The number of pixels by which to offset the uploaded image from the top.',
								'type'        => 'integer',
							),
						),
					),
				),
			);

			$this->routes['/account/update_profile_image.json'] = array(
				'methods' => array(
					'POST' => array(
						'description'          => 'Updates the authenticating user’s profile image.',
						'needs_authentication' => true,
						'params'               => array(
							'image'            => array(
								'required'    => true,
								'description' => 'The avatar image for the profile, base64-encoded. Must be a valid GIF, JPG, or PNG image of less than 700 kilobytes in size.',
								'type'        => 'string',
							),
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'skip_status'      => array(
								'description' => 'When set to either true, t or 1 statuses will not be included in the returned user object.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
						),
					),
				),
			);

			$this->routes['/account/verify_credentials.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns an HTTP 200 OK response code and a representation of the requesting user if authentication was successful; returns a 401 status code and an error message if not. Use this method to test if supplied user credentials are valid.',
						'needs_authentication' => true,
						'params'               => array(
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'skip_status'      => array(
								'description' => 'When set to either true, t or 1 statuses will not be included in the returned user object.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'include_email'    => array(
								'description' => 'When set to true email will be returned in the user objects as a string.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
						),
					),
				),
			);

			$this->routes['/application/rate_limit_status.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns the current rate limits for methods belonging to the specified resource families.',
						'needs_authentication' => true,
						'params'               => array(
							'resources' => array(
								'description' => 'A comma-separated list of resource families you want to know the current rate limit disposition for.',
								'type'        => 'string',
							),
						),
					),
				),
			);

			$this->routes['/blocks/create.json'] = array(
				'methods' => array(
					'POST' => array(
						'description'          => 'Blocks the specified user from following the authenticating user.',
						'needs_authentication' => true,
						'params'               => array(
							'screen_name'      => array(
								'description' => 'The screen name of the potentially blocked user. Helpful for disambiguating when a valid screen name is also a user ID.',
								'type'        => 'string',
							),
							'user_id'          => array(
								'description' => 'The ID of the potentially blocked user. Helpful for disambiguating when a valid user ID is also a valid screen name.',
								'type'        => 'string',
							),
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'skip_status'      => array(
								'description' => 'When set to either true, t or 1 statuses will not be included in the returned user object.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
						),
					),
				),
			);

			$this->routes['/blocks/destroy.json'] = array(
				'methods' => array(
					'POST' => array(
						'description'          => 'Un-blocks the user specified in the ID parameter for the authenticating user.',
						'needs_authentication' => true,
						'params'               => array(
							'screen_name'      => array(
								'description' => 'The screen name of the potentially blocked user. Helpful for disambiguating when a valid screen name is also a user ID.',
								'type'        => 'string',
							),
							'user_id'          => array(
								'description' => 'The ID of the potentially blocked user. Helpful for disambiguating when a valid user ID is also a valid screen name.',
								'type'        => 'string',
							),
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'skip_status'      => array(
								'description' => 'When set to either true, t or 1 statuses will not be included in the returned user object.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
						),
					),
				),
			);

			$this->routes['/blocks/ids.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns an array of numeric user ids the authenticating user is blocking.',
						'needs_authentication' => true,
						'params'               => array(
							'stringify_ids' => array(
								'description' => 'Many programming environments will not consume our ids due to their size. Provide this option to have ids returned as strings instead.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'cursor'        => array(
								'description' => 'Causes the list of IDs to be broken into pages of no more than 5000 IDs at a time and determines the paging.',
								'type'        => 'string',
							),
						),
					),
				),
			);

			$this->routes['/blocks/list.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns a collection of user objects that the authenticating user is blocking.',
						'needs_authentication' => true,
						'params'               => array(
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'skip_status'      => array(
								'description' => 'When set to either true, t or 1 statuses will not be included in the returned user objects.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'cursor'           => array(
								'description' => 'Causes the list of blocked users to be broken into pages of no more than 5000 IDs at a time and determines the paging.',
								'type'        => 'string',
							),
						),
					),
				),
			);

			//TODO: everything from `collections` to `saved_searches`

			$this->routes['/search/tweets.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns a collection of relevant Tweets matching a specified query.',
						'needs_authentication' => true,
						'params'               => array(
							'q'                => array(
								'required'    => true,
								'description' => 'A UTF-8, URL-encoded search query of 500 characters maximum, including operators. Queries may additionally be limited by complexity.',
								'type'        => 'string',
							),
							'geocode'          => array(
								'description' => 'Returns tweets by users located within a given radius of the given latitude/longitude. The parameter value is specified by "latitude,longitude,radius", where radius units must be specified as either "mi" (miles) or "km" (kilometers).',
								'type'        => 'string',
							),
							'lang'             => array(
								'description' => 'Restricts tweets to the given language, given by an ISO 639-1 code. Language detection is best-effort.',
								'type'        => 'string',
							),
							'locale'           => array(
								'description' => 'Specify the language of the query you are sending (only "ja" is currently effective).',
								'type'        => 'string',
							),
							'result_type'      => array(
								'description' => 'Specifies what type of search results you would prefer to receive.',
								'type'        => 'string',
								'enum'        => array( 'mixed', 'recent', 'popular' ),
								'default'     => 'mixed',
							),
							'count'            => array(
								'description' => 'The number of tweets to return per page, up to a maximum of 100.',
								'type'        => 'integer',
								'minimum'     => 1,
								'maximum'     => 100,
								'default'     => 15,
							),
							'until'            => array(
								'description' => 'Returns tweets created before the given date. Date should be formatted as YYYY-MM-DD.',
								'type'        => 'string',
							),
							'since_id'         => array(
								'description' => 'Returns results with an ID greater than (that is, more recent than) the specified ID.',
								'type'        => 'string',
							),
							'max_id'           => array(
								'description' => 'Returns results with an ID less than (that is, older than) or equal to the specified ID.',
								'type'        => 'string',
							),
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/destroy/(?P<id>[\d]+).json'] = array(
				'primary_params' => array(
					'id' => array(
						'description' => 'The numerical ID of the desired status.',
					),
				),
				'methods'        => array(
					'POST' => array(
						'description'          => 'Destroys the status specified by the required ID parameter. Returns the destroyed status if successful.',
						'needs_authentication' => true,
						'params'               => array(
							'trim_user' => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/home_timeline.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns a collection of the most recent Tweets and retweets posted by the authenticating user and the users they follow.',
						'needs_authentication' => true,
						'params'               => array(
							'count'            => array(
								'description' => 'Specifies the number of records to retrieve. Must be less than or equal to 200.',
								'type'        => 'integer',
								'minimum'     => 1,
								'maximum'     => 200,
								'default'     => 20,
							),
							'since_id'         => array(
								'description' => 'Returns results with an ID greater than (that is, more recent than) the specified ID.',
								'type'        => 'string',
							),
							'max_id'           => array(
								'description' => 'Returns results with an ID less than (that is, older than) or equal to the specified ID.',
								'type'        => 'string',
							),
							'trim_user'        => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'exclude_replies'  => array(
								'description' => 'This parameter will prevent replies from appearing in the returned timeline.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/lookup.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns fully-hydrated Tweet objects for up to 100 Tweets per request, as specified by comma-separated values passed to the id parameter.',
						'needs_authentication' => true,
						'params'               => array(
							'id'               => array(
								'required'    => true,
								'description' => 'A comma separated list of Tweet IDs, up to 100 are allowed in a single request.',
								'type'        => 'string',
							),
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'trim_user'        => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'map'              => array(
								'description' => 'When using the map parameter, Tweets that do not exist or cannot be viewed by the current user will still have their key represented but with an explicitly null value paired with it.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/mentions_timeline.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns the 20 most recent mentions (Tweets containing a users’s @screen_name) for the authenticating user.',
						'needs_authentication' => true,
						'params'               => array(
							'count'            => array(
								'description' => 'Specifies the number of Tweets to try and retrieve, up to a maximum of 200.',
								'type'        => 'integer',
								'minimum'     => 1,
								'maximum'     => 200,
								'default'     => 20,
							),
							'since_id'         => array(
								'description' => 'Returns results with an ID greater than (that is, more recent than) the specified ID.',
								'type'        => 'string',
							),
							'max_id'           => array(
								'description' => 'Returns results with an ID less than (that is, older than) or equal to the specified ID.',
								'type'        => 'string',
							),
							'trim_user'        => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'include_entities' => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/retweet/(?P<id>[\d]+).json'] = array(
				'primary_params' => array(
					'id' => array(
						'description' => 'The numerical ID of the desired status.',
					),
				),
				'methods'        => array(
					'POST' => array(
						'description'          => 'Retweets a tweet. Returns the original tweet with retweet details embedded.',
						'needs_authentication' => true,
						'params'               => array(
							'trim_user' => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/retweeters/ids.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns a collection of up to 100 user IDs belonging to users who have retweeted the Tweet specified by the id parameter.',
						'needs_authentication' => true,
						'params'               => array(
							'id'            => array(
								'required'    => true,
								'description' => 'The numerical ID of the desired status.',
								'type'        => 'string',
							),
							'cursor'        => array(
								'description' => 'Causes the list of IDs to be broken into pages of no more than 100 IDs at a time and determines the paging.',
								'type'        => 'string',
							),
							'stringify_ids' => array(
								'description' => 'Many programming environments will not consume Tweet ids due to their size. Provide this option to have ids returned as strings instead.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/retweets/(?P<id>[\d]+).json'] = array(
				'primary_params' => array(
					'id' => array(
						'description' => 'The numerical ID of the desired status.',
					),
				),
				'methods'        => array(
					'GET' => array(
						'description'          => 'Returns a collection of the 100 most recent retweets of the Tweet specified by the id parameter.',
						'needs_authentication' => true,
						'params'               => array(
							'count'     => array(
								'description' => 'Specifies the number of records to retrieve. Must be less than or equal to 100.',
								'type'        => 'integer',
								'minimum'     => 1,
								'maximum'     => 100,
								'default'     => 20,
							),
							'trim_user' => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/retweets_of_me.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns the most recent Tweets authored by the authenticating user that have been retweeted by others.',
						'needs_authentication' => true,
						'params'               => array(
							'count'                 => array(
								'description' => 'Specifies the number of records to retrieve. Must be less than or equal to 100.',
								'type'        => 'integer',
								'minimum'     => 1,
								'maximum'     => 100,
								'default'     => 20,
							),
							'since_id'              => array(
								'description' => 'Returns results with an ID greater than (that is, more recent than) the specified ID.',
								'type'        => 'string',
							),
							'max_id'                => array(
								'description' => 'Returns results with an ID less than (that is, older than) or equal to the specified ID.',
								'type'        => 'string',
							),
							'trim_user'             => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'include_entities'      => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'include_user_entities' => array(
								'description' => 'The user entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/show/(?P<id>[\d]+).json'] = array(
				'primary_params' => array(
					'id' => array(
						'description' => 'The numerical ID of the desired Tweet.',
					),
				),
				'methods'        => array(
					'GET' => array(
						'description'          => 'Returns a single Tweet, specified by the id parameter.',
						'needs_authentication' => true,
						'params'               => array(
							'include_my_retweet' => array(
								'description' => 'When set to either true, t or 1, any Tweets returned that have been retweeted by the authenticating user will include an additional current_user_retweet node, containing the ID of the source status for the retweet.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'trim_user'         => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'include_entities'  => array(
								'description' => 'The entities node will not be included when set to false.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/unretweet/(?P<id>[\d]+).json'] = array(
				'primary_params' => array(
					'id' => array(
						'description' => 'The numerical ID of the desired status.',
					),
				),
				'methods'        => array(
					'POST' => array(
						'description'          => 'Untweets a retweeted status. Returns the original Tweet with retweet details embedded.',
						'needs_authentication' => true,
						'params'               => array(
							'trim_user' => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
						),
					),
				),
			);

			$this->routes['/statuses/update.json'] = array(
				'methods' => array(
					'POST' => array(
						'description'          => 'Updates the authenticating user’s current status, also known as Tweeting.',
						'needs_authentication' => true,
						'params'               => array(
							'status'                => array(
								'required'    => true,
								'description' => 'The text of your status update, typically up to 140 characters. URL encode as necessary. t.co link wrapping may affect character counts.',
								'type'        => 'string',
							),
							'in_reply_to_status_id' => array(
								'description' => 'The ID of an existing status that the update is in reply to. You must include @username, where username is the author of the referenced Tweet, within the update.',
								'type'        => 'string',
							),
							'possibly_sensitive'    => array(
								'description' => 'If you upload Tweet media that might be considered sensitive content such as nudity, violence, or medical procedures, you should set this value to true.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'lat'                   => array(
								'description' => 'The latitude of the location this Tweet refers to.',
								'type'        => 'float',
								'minimum'     => -90.0,
								'maximum'     => 90.0,
							),
							'long'                  => array(
								'description' => 'The longitude of the location this Tweet refers to.',
								'type'        => 'float',
								'minimum'     => -180.0,
								'maximum'     => 180.0,
							),
							'place_id'              => array(
								'description' => 'A place in the world.',
								'type'        => 'string',
							),
							'display_coordinates'   => array(
								'description' => 'Whether or not to put a pin on the exact coordinates a Tweet has been sent from.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'trim_user'             => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'media_ids'             => array(
								'description' => 'A list of media_ids to associate with the Tweet. You may include up to 4 photos or 1 animated GIF or 1 video in a Tweet.',
								'type'        => 'string',
							),
						),
					),
				),
			);

			$this->routes['/statuses/user_timeline.json'] = array(
				'methods' => array(
					'GET' => array(
						'description'          => 'Returns a collection of the most recent Tweets and retweets posted by the authenticating user and the users they follow.',
						'needs_authentication' => true,
						'params'               => array(
							'user_id'          => array(
								'description' => 'The ID of the user for whom to return results for.',
								'type'        => 'string',
							),
							'screen_name'      => array(
								'description' => 'The screen name of the user for whom to return results for.',
								'type'        => 'string',
							),
							'count'            => array(
								'description' => 'Specifies the number of Tweets to try and retrieve, up to a maximum of 200 per distinct request.',
								'type'        => 'integer',
								'minimum'     => 1,
								'maximum'     => 200,
								'default'     => 20,
							),
							'since_id'            => array(
								'description' => 'Returns results with an ID greater than (that is, more recent than) the specified ID.',
								'type'        => 'string',
							),
							'max_id'              => array(
								'description' => 'Returns results with an ID less than (that is, older than) or equal to the specified ID.',
								'type'        => 'string',
							),
							'trim_user'           => array(
								'description' => 'When set to either true, t or 1 each Tweet returned in a timeline will include a user object including only the status authors numerical ID.',
								'type'        => 'string',
								'enum'        => array( 'true', 't', '1', 'false', 'f', '0' ),
							),
							'exclude_replies'     => array(
								'description' => 'This parameter will prevent replies from appearing in the returned timeline.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'contributor_details' => array(
								'description' => 'This parameter enhances the contributors element of the status response to include the screen_name of the contributor.',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
							'include_rts'         => array(
								'description' => 'When set to false , the timeline will strip any native retweets (though they will still count toward both the maximal length of the timeline and the slice selected by the count parameter).',
								'type'        => 'string',
								'enum'        => array( 'true', 'false' ),
							),
						),
					),
				),
			);

			//TODO: everything from `trends` to `users`
		}
	}

}
