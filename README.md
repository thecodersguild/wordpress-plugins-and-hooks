#WordPress Plugins and Hooks
The Atlanta WordPress Coder's Guild
- Presenters: @mikeschinkel / @wpscholar
- Twitter: @thecodersguild
- Hashtag: #wppluginsandhooks

##Outline
0. Intro
	- What is a Plugin?
	- What is a Hook?
	- Compare Plugins vs. Themes vs. Core
1. Minimum Viable Plugin
	- The Plugin Directory
	- echo `<font color="red" size="7">Hello World!</font>`
	- Activation
2. Simple Plugin 
	- Using `'the_content'` hook
	- Print Copyright on every post
3. The Must-Use Plugin Directory
	- The `plugin-loader.php`
4. Using Classes for Plugins
	- EventPressRedux
5. Add Post Types
	- Event
	- Venue
	- Registration	
6. Data Entry Fields
	- Start, End, Venue
	- Use `edit_form_after_title` hook
		- Instead of a Metabox - we'll show that too
7. Enqueue Scripts and CSS
	- Enqueue Select2.js for Venue
	- Add in Footer
9. Register an Email Address for an Event
	- Using WP-AJAX and 
	- Registration post type.


##Repository
- [The Plugin Repository](https://github.com/thecodersguild/wordpress-plugins-and-hooks) on GitHub

##References
- [Codex Plugins API/Actions Reference](https://codex.wordpress.org/Plugin_API/Action_Reference)
- [Hooks Reference](https://developer.wordpress.org/reference/hooks/)
- [Plugin File Header Fields](https://codex.wordpress.org/File_Header)