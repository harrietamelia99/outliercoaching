<?php
/**
 * Outlier Coaching — theme bootstrap.
 *
 * Keeps this file lean: enqueues, thumbnail support, and the landing page
 * field group (classic meta box — fully usable alongside the block editor).
 *
 * @package Outlier_Collective
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OC_THEME_VERSION', '1.0.0' );

/**
 * Bundled Outlier Coaching lockup (OL Coaching — Light / Dark from brand SVGs).
 * Used when the corresponding media library field is left empty.
 *
 * @param string $which light|dark
 */
function oc_bundled_logo_url( $which = 'light' ) {
	$file = 'light' === $which ? 'logo-outlier-coaching-light.svg' : 'logo-outlier-coaching-dark.svg';
	$path = get_template_directory() . '/assets/' . $file;
	$uri  = get_template_directory_uri() . '/assets/' . $file;
	$ver  = is_readable( $path ) ? (string) filemtime( $path ) : OC_THEME_VERSION;
	return add_query_arg( 'ver', rawurlencode( $ver ), $uri );
}

/**
 * Light lockup for dark backgrounds (hero).
 */
function oc_bundled_logo_light_url() {
	return oc_bundled_logo_url( 'light' );
}

/**
 * Dark lockup for light backgrounds (e.g. cards).
 */
function oc_bundled_logo_dark_url() {
	return oc_bundled_logo_url( 'dark' );
}

/**
 * Filenames for the only bundled photography (assets/site/). Order is stable for fallbacks.
 *
 * @return string[]
 */
function oc_bundled_site_photo_filenames() {
	return array(
		'photo-1.png',
		'photo-2.png',
		'photo-3.png',
		'photo-4.png',
		'photo-5.png',
	);
}

/**
 * URL for a bundled site photo by index (0-based, wraps modulo count).
 *
 * @param int $index Which photo in oc_bundled_site_photo_filenames().
 * @return string URL or empty if file missing
 */
function oc_bundled_site_photo_url( $index ) {
	$files = oc_bundled_site_photo_filenames();
	$n       = count( $files );
	if ( $n < 1 ) {
		return '';
	}
	$index    = (int) $index;
	$index    = ( ( $index % $n ) + $n ) % $n;
	$filename = $files[ $index ];
	$path     = get_template_directory() . '/assets/site/' . $filename;
	if ( ! is_readable( $path ) ) {
		return '';
	}
	$uri = get_template_directory_uri() . '/assets/site/' . $filename;
	$ver = (string) filemtime( $path );
	return add_query_arg( 'ver', rawurlencode( $ver ), $uri );
}

/**
 * Inline map pin SVG for offering card location row (colour via currentColor).
 *
 * @return string Safe HTML.
 */
function oc_offering_location_pin_svg() {
	$svg = '<svg class="offering-card__pin-svg" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" width="15" height="15" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 22s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11zm0-14.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5z"/></svg>';

	return wp_kses(
		$svg,
		array(
			'svg'  => array(
				'class'       => true,
				'xmlns'       => true,
				'viewbox'     => true,
				'width'       => true,
				'height'      => true,
				'fill'        => true,
				'aria-hidden' => true,
				'focusable'   => true,
			),
			'path' => array( 'd' => true ),
		)
	);
}

/**
 * Versioned URL for testimonial card quote-mark graphic (bundled SVG).
 *
 * @return string URL or empty if file missing.
 */
function oc_testimonial_quote_marks_url() {
	$path = get_template_directory() . '/assets/testimonial-quote-marks.svg';
	if ( ! is_readable( $path ) ) {
		return '';
	}
	$uri = get_theme_file_uri( 'assets/testimonial-quote-marks.svg' );
	$ver = (string) filemtime( $path );
	return add_query_arg( 'ver', rawurlencode( $ver ), $uri );
}

/**
 * Default copy and URLs for the landing template (placeholders).
 *
 * @return array<string, string>
 */
function oc_landing_default_meta() {
	return array(
		'oc_hero_headline'       => "If you're looking for something different,\nfind it here.",
		'oc_hero_subhead'       => 'Leadership development, life design, experiences and adventures.',
		'oc_hero_cta_text'       => "Let's begin",
		'oc_hero_cta_url'        => '#contact',

		'oc_problem_s1'          => 'The change you want that hasn\'t happened yet.',
		'oc_problem_s2'          => 'We help you make it happen.',
		'oc_problem_s3'          => 'Stay and thrive or leave well.',
		'oc_problem_orange_word' => 'thrive',

		'oc_path_intro'          => 'We will find you where you are and walk with you where you\'re going.',

		'oc_step1_label'         => 'Conversation',
		'oc_step1_desc'          => 'Get in touch, speak to a real person. After 20 minutes you\'ll know.',
		'oc_step2_label'         => 'Coaching',
		'oc_step2_desc'          => 'Online at a time that suits you, or in person in North Devon.',
		'oc_step3_label'         => 'Experiences & Adventures',
		'oc_step3_desc'          => 'Bring yourself or your team. 1 or 3 days in North Devon, a week in Ghana.',
		'oc_step4_label'         => 'Collective',
		'oc_step4_desc'          => '',

		'oc_offer1_title'        => 'Online Sessions',
		'oc_offer1_location'     => 'Anywhere',
		'oc_offer1_text'         => "Accessible anywhere with a fully qualified experienced coach. Leadership coaching, leadership development, life design.",
		'oc_offer1_coach_1_label'  => 'Life Design',
		'oc_offer1_coach_1_text'   => 'Here and now. For people who want something different and are here to find it.',
		'oc_offer1_coach_2_label'  => 'Work and Career',
		'oc_offer1_coach_2_text'   => 'Stop waiting for the job to get better. Stay and thrive, or leave well. Let\'s work out which.',
		'oc_offer1_coach_3_label'  => 'Leadership',
		'oc_offer1_coach_3_text'   => 'You got the job for a reason. Time to lead like it.',
		'oc_offer1_coach_4_label'  => 'Team Development',
		'oc_offer1_coach_4_text'   => 'Great teams close the door and sort it out together. Let\'s do that.',
		'oc_offer2_title'        => 'In Person Experiences',
		'oc_offer2_location'     => 'North Devon',
		'oc_offer2_text'         => 'At the beach, in the woods, by the fire, next to water. Bring yourself or your team. 1 or 3 days at Ashbarton Estate, North Devon.',
		'oc_offer3_title'        => 'Outlier Adventures',
		'oc_offer3_location'     => 'Remote · West Africa',
		'oc_offer3_text'         => 'Get out the office, turn your phone off. One week adventure in a remote location finishing in West Africa.',
		'oc_offer3_retreat_1_title' => 'Ghana 26',
		'oc_offer3_retreat_1_text'  => 'Coaching, teaching and leadership in Mankoadze, Ghana.',
		'oc_offer3_retreat_1_url'   => '',
		'oc_offer3_retreat_2_title' => 'Devon 26',
		'oc_offer3_retreat_2_text'  => 'Deliberate Leadership. North Devon. May to July 2026.',
		'oc_offer3_retreat_2_url'   => '',
		'oc_offer3_retreat_3_title' => 'Portugal 26',
		'oc_offer3_retreat_3_text'  => 'Life can be better workshops. Southern Portugal. September 2026.',
		'oc_offer3_retreat_3_url'   => '',
		'oc_offer3_pullquote'      => 'It was an incredible experience. A real shift in focus on what matters.',
		'oc_offer5_title'        => 'Team Coaching',
		'oc_offer5_location'     => 'Anywhere',
		'oc_offer5_text'         => 'High support, high challenge. Great teams close the door and solve problems together. Let\'s do that.',
		'oc_offer6_title'        => 'Talks and Workshops',
		'oc_offer6_location'     => 'Anywhere',
		'oc_offer6_text'         => "Got a group that needs to hear this?\nWe do talks.",
		'oc_offerings_learn_more_url' => '#contact',

		'oc_philosophy_text'     => '',

		'oc_testimonials_label'  => 'What people say',
		'oc_testimonial_1'       => 'Outlier is different. Good different. Very good different. I\'ve worked with a variety of coaches over the years but can honestly say that I have developed the most - both personally and professionally - with Outlier. The approach is unique. I am encouraged to think deeply and do better and be better.',
		'oc_testimonial_2'       => 'Want a different type of coach? Outlier gets to the core of the issue whilst getting you to reflect, question, laugh, cry (in a good way), and solve the problem. I leave feeling challenged, like I\'ve had therapy, leadership development and great fun. Book it - it\'s rare to find someone of this quality.',
		'oc_testimonial_3'       => 'Outlier has been instrumental in transforming the way I think, behave and manage my leadership role. They are so in tune with what you think and feel and can very quickly delve deeper into the why and what but ultimately the how to make things better.',
		'oc_testimonial_4'       => 'Outlier\'s sessions feel more like soul-searching, which feeds into both the logical and emotional brain. Inherently helpful and I am already grateful for Outlier\'s sincere interest and commitment to helping me grow as a leader, and a person.',

		'oc_upcoming_talks_label' => 'Upcoming experiences',
		'oc_utalk_1_title'       => 'Ghana Coaching Week',
		'oc_utalk_1_meta'        => '8th–15th November',
		'oc_utalk_1_desc'        => '',
		'oc_utalk_1_url'         => '',
		'oc_utalk_2_title'       => '',
		'oc_utalk_2_meta'        => '',
		'oc_utalk_2_desc'        => '',
		'oc_utalk_2_url'         => '',
		'oc_utalk_3_title'       => '',
		'oc_utalk_3_meta'        => '',
		'oc_utalk_3_desc'        => '',
		'oc_utalk_3_url'         => '',
		'oc_utalk_4_title'       => '',
		'oc_utalk_4_meta'        => '',
		'oc_utalk_4_desc'        => '',
		'oc_utalk_4_url'         => '',

		'oc_talks_text'          => "Got a group that needs to hear this?\nWe do talks.",
		'oc_talks_cta_text'      => 'Get in touch',
		'oc_talks_cta_url'       => '#contact',

		'oc_contact_heading'     => 'Let\'s have a conversation.',
		'oc_contact_body'        => 'Connect to a real person. Book a free 20 minute slot and let\'s begin.',
		'oc_confidentiality_text' => 'Outlier Coaching is 100% confidential. You are not a case study, no notes are kept, no data is studied, no trends are looked at. If you\'re looking for different, find it here.',
		'oc_calendly_url'        => 'https://calendly.com/outlier-coaching/outlier-discovery-call',
		'oc_calendly_label'      => 'Book a discovery call',
		'oc_email_url'           => 'mailto:bookings@outliercoaching.co.uk',
		'oc_email_label'         => 'Send an email',

		'oc_footer_copy'         => '© Outlier Coaching',
	);
}

/**
 * Retrieve a landing meta value with fallback to defaults.
 *
 * @param int         $post_id Post ID.
 * @param string      $key     Meta key.
 * @param string|null $default Optional explicit default.
 */
function oc_get_landing( $post_id, $key, $default = null ) {
	$val = get_post_meta( $post_id, $key, true );
	if ( $val !== '' && $val !== null ) {
		return $val;
	}
	$defaults = oc_landing_default_meta();
	if ( $default !== null ) {
		return $default;
	}
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Join the last two words with a non-breaking space so the last line is not a single word.
 *
 * @param string $text Plain text only (no HTML).
 * @return string Same text with U+00A0 before the final word when there are at least two words.
 */
function oc_soft_break_widow( $text ) {
	$text = trim( (string) $text );
	if ( $text === '' || ! preg_match( '/\s/u', $text ) ) {
		return $text;
	}
	$words = preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY );
	if ( count( $words ) < 2 ) {
		return $text;
	}
	$last = array_pop( $words );
	return implode( ' ', $words ) . "\xc2\xa0" . $last;
}

/**
 * Escape plain text and turn line breaks into <br /> (widow control per line).
 *
 * @param string $text Plain text; use \n for intentional line breaks.
 * @return string Safe HTML fragment.
 */
function oc_esc_html_with_br( $text ) {
	$text = (string) $text;
	if ( $text === '' ) {
		return '';
	}
	$lines = preg_split( '/\r\n|\r|\n/', $text );
	$parts = array();
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( $line === '' ) {
			continue;
		}
		$parts[] = esc_html( oc_soft_break_widow( $line ) );
	}
	return implode( '<br />', $parts );
}

/**
 * Wrap the first occurrence of “different” (case-insensitive) plus trailing .,!?… in the hero accent span.
 *
 * @param string $text Plain text only (typically after oc_soft_break_widow()).
 * @return string Safe HTML fragment.
 */
function oc_format_hero_accent_word( $text ) {
	$text = (string) $text;
	if ( $text === '' ) {
		return '';
	}
	$safe = esc_html( $text );
	$out  = preg_replace_callback(
		'/(?i)\bdifferent\b([.,!?…]*)/u',
		static function ( $m ) {
			return '<span class="hero-accent">' . $m[0] . '</span>';
		},
		$safe,
		1
	);
	return $out !== null ? $out : $safe;
}

/**
 * Escape talks headline lines to <br /> and wrap “We do talks” (case-insensitive) + trailing punctuation for scroll accent (main.js + .talks-accent).
 *
 * @param string $text Plain text; use \n between lines.
 * @return string Safe HTML fragment.
 */
function oc_format_talks_headline_html( $text ) {
	$text = (string) $text;
	if ( $text === '' ) {
		return '';
	}
	$lines = preg_split( '/\r\n|\r|\n/', $text );
	$parts = array();
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( $line === '' ) {
			continue;
		}
		$escaped = esc_html( oc_soft_break_widow( $line ) );
		$with    = preg_replace_callback(
			'/(?i)(\bwe\s+do\s+talks\b)([.,!?…]*)/u',
			static function ( $m ) {
				return '<span class="talks-accent">' . $m[1] . $m[2] . '</span>';
			},
			$escaped,
			1
		);
		$parts[] = $with !== null ? $with : $escaped;
	}
	return implode( '<br />', $parts );
}

/**
 * Theme supports and image sizes used by the landing layout.
 */
function oc_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_image_size( 'oc-hero-col', 960, 1200, true );
	add_image_size( 'oc-offering', 960, 540, true );
}
add_action( 'after_setup_theme', 'oc_theme_setup' );

/**
 * Landing: hide body until window load + GSAP reveal (paired with main.js + style.css).
 */
function oc_landing_body_boot_class( $classes ) {
	if ( is_page_template( 'page-landing.php' ) ) {
		$classes[] = 'oc-landing-boot';
	}
	return $classes;
}
add_filter( 'body_class', 'oc_landing_body_boot_class' );

/**
 * Landing: GSAP stack must execute in order — strip defer/async WordPress may inject.
 */
function oc_landing_script_loader_tag( $tag, $handle, $src ) {
	if ( ! is_page_template( 'page-landing.php' ) ) {
		return $tag;
	}
	$handles = array(
		'gsap',
		'gsap-scrolltrigger',
		'gsap-scrollsmoother',
		'gsap-splittext',
		'lenis',
		'split-type',
		'outlier-collective-main',
	);
	if ( ! in_array( $handle, $handles, true ) ) {
		return $tag;
	}
	$tag = preg_replace( '/\s+defer(=[\'"]defer[\'"])?/i', '', $tag );
	$tag = preg_replace( '/\s+async(=[\'"]async[\'"])?/i', '', $tag );
	return $tag;
}
add_filter( 'script_loader_tag', 'oc_landing_script_loader_tag', 99, 3 );

/**
 * Faster Google Fonts handshake on all front-end views.
 */
function oc_resource_hints_fonts( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'oc_resource_hints_fonts', 10, 2 );

/**
 * Enqueue theme stylesheet site-wide (minimal fallback pages use the same tokens).
 */
function oc_enqueue_theme_styles() {
	wp_enqueue_style(
		'oc-theme-fonts',
		'https://fonts.googleapis.com/css2?family=Onest:wght@100..900&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'outlier-collective',
		get_stylesheet_uri(),
		array( 'oc-theme-fonts' ),
		OC_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'oc_enqueue_theme_styles' );

/**
 * Landing-only: GSAP stack from CDN + Lenis/SplitType fallbacks.
 * ScrollSmoother & SplitText are GreenSock Club files — public cdnjs URLs often 404. If so, Lenis +
 * SplitType still load. To use official Club plugins, add ScrollSmoother.min.js and SplitText.min.js
 * under /js/vendor/ and enqueue them before main.js (see oc_maybe_enqueue_gsap_club).
 */
function oc_maybe_enqueue_gsap_club() {
	$sm = get_template_directory() . '/js/vendor/ScrollSmoother.min.js';
	$st = get_template_directory() . '/js/vendor/SplitText.min.js';
	if ( is_readable( $sm ) ) {
		wp_enqueue_script(
			'gsap-scrollsmoother',
			get_template_directory_uri() . '/js/vendor/ScrollSmoother.min.js',
			array( 'gsap-scrolltrigger' ),
			(string) filemtime( $sm ),
			true
		);
	} else {
		wp_enqueue_script(
			'gsap-scrollsmoother',
			'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollSmoother.min.js',
			array( 'gsap-scrolltrigger' ),
			'3.12.5',
			true
		);
	}
	if ( is_readable( $st ) ) {
		wp_enqueue_script(
			'gsap-splittext',
			get_template_directory_uri() . '/js/vendor/SplitText.min.js',
			array( 'gsap' ),
			(string) filemtime( $st ),
			true
		);
	} else {
		wp_enqueue_script(
			'gsap-splittext',
			'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/SplitText.min.js',
			array( 'gsap' ),
			'3.12.5',
			true
		);
	}
}

function oc_enqueue_landing_script() {
	if ( ! is_page_template( 'page-landing.php' ) ) {
		return;
	}
	$gsap_v = '3.12.5';
	wp_enqueue_script(
		'gsap',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/' . $gsap_v . '/gsap.min.js',
		array(),
		$gsap_v,
		true
	);
	wp_enqueue_script(
		'gsap-scrolltrigger',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/' . $gsap_v . '/ScrollTrigger.min.js',
		array( 'gsap' ),
		$gsap_v,
		true
	);
	oc_maybe_enqueue_gsap_club();
	wp_enqueue_script(
		'lenis',
		'https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js',
		array(),
		'1.1.18',
		true
	);
	wp_enqueue_script(
		'split-type',
		'https://cdn.jsdelivr.net/npm/split-type@0.3.4/umd/index.min.js',
		array(),
		'0.3.4',
		true
	);
	wp_enqueue_script(
		'outlier-collective-main',
		get_template_directory_uri() . '/js/main.js',
		array(
			'gsap',
			'gsap-scrolltrigger',
			'gsap-scrollsmoother',
			'gsap-splittext',
			'lenis',
			'split-type',
		),
		OC_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'oc_enqueue_landing_script' );

/**
 * Admin: media uploader for image ID fields.
 */
function oc_landing_admin_assets( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script(
		'outlier-landing-admin',
		get_template_directory_uri() . '/js/admin-landing.js',
		array( 'jquery' ),
		OC_THEME_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'oc_landing_admin_assets' );

/**
 * List of all landing meta keys (image IDs are integers; rest are strings).
 *
 * @return string[]
 */
function oc_landing_meta_keys() {
	return array(
		'oc_hero_bg_id',
		'oc_logo_white_id',
		'oc_logo_black_id',
		'oc_hero_headline',
		'oc_hero_subhead',
		'oc_hero_cta_text',
		'oc_hero_cta_url',
		'oc_problem_s1',
		'oc_problem_s2',
		'oc_problem_s3',
		'oc_problem_orange_word',
		'oc_path_intro',
		'oc_step1_label',
		'oc_step1_desc',
		'oc_step2_label',
		'oc_step2_desc',
		'oc_step3_label',
		'oc_step3_desc',
		'oc_step4_label',
		'oc_step4_desc',
		'oc_offer1_title',
		'oc_offer1_location',
		'oc_offer1_text',
		'oc_offer1_coach_1_label',
		'oc_offer1_coach_1_text',
		'oc_offer1_coach_2_label',
		'oc_offer1_coach_2_text',
		'oc_offer1_coach_3_label',
		'oc_offer1_coach_3_text',
		'oc_offer1_coach_4_label',
		'oc_offer1_coach_4_text',
		'oc_offer1_img',
		'oc_offer2_title',
		'oc_offer2_location',
		'oc_offer2_text',
		'oc_offer2_img',
		'oc_offer3_title',
		'oc_offer3_location',
		'oc_offer3_text',
		'oc_offer3_retreat_1_title',
		'oc_offer3_retreat_1_text',
		'oc_offer3_retreat_1_url',
		'oc_offer3_retreat_2_title',
		'oc_offer3_retreat_2_text',
		'oc_offer3_retreat_2_url',
		'oc_offer3_retreat_3_title',
		'oc_offer3_retreat_3_text',
		'oc_offer3_retreat_3_url',
		'oc_offer3_pullquote',
		'oc_offer3_img',
		'oc_offer5_title',
		'oc_offer5_location',
		'oc_offer5_text',
		'oc_offer5_img',
		'oc_offer6_title',
		'oc_offer6_location',
		'oc_offer6_text',
		'oc_offer6_img',
		'oc_offerings_learn_more_url',
		'oc_philosophy_text',
		'oc_testimonials_label',
		'oc_testimonial_1',
		'oc_testimonial_2',
		'oc_testimonial_3',
		'oc_testimonial_4',
		'oc_upcoming_talks_label',
		'oc_utalk_1_title',
		'oc_utalk_1_meta',
		'oc_utalk_1_desc',
		'oc_utalk_1_url',
		'oc_utalk_2_title',
		'oc_utalk_2_meta',
		'oc_utalk_2_desc',
		'oc_utalk_2_url',
		'oc_utalk_3_title',
		'oc_utalk_3_meta',
		'oc_utalk_3_desc',
		'oc_utalk_3_url',
		'oc_utalk_4_title',
		'oc_utalk_4_meta',
		'oc_utalk_4_desc',
		'oc_utalk_4_url',
		'oc_talks_text',
		'oc_talks_cta_text',
		'oc_talks_cta_url',
		'oc_contact_heading',
		'oc_contact_body',
		'oc_confidentiality_text',
		'oc_email_url',
		'oc_email_label',
		'oc_calendly_url',
		'oc_calendly_label',
		'oc_footer_logo_id',
		'oc_footer_copy',
	);
}

/**
 * Meta keys stored as attachment IDs (integers).
 *
 * @return string[]
 */
function oc_landing_integer_meta_keys() {
	return array(
		'oc_hero_bg_id',
		'oc_logo_white_id',
		'oc_logo_black_id',
		'oc_footer_logo_id',
		'oc_offer1_img',
		'oc_offer2_img',
		'oc_offer3_img',
		'oc_offer5_img',
		'oc_offer6_img',
	);
}

/**
 * Register post meta for REST visibility (optional integrations / future use).
 */
function oc_register_landing_post_meta() {
	$int_keys = oc_landing_integer_meta_keys();
	$keys     = oc_landing_meta_keys();
	foreach ( $keys as $key ) {
		$type = in_array( $key, $int_keys, true ) ? 'integer' : 'string';
		register_post_meta(
			'page',
			$key,
			array(
				'type'          => $type,
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function () {
					return current_user_can( 'edit_pages' );
				},
			)
		);
	}
}
add_action( 'init', 'oc_register_landing_post_meta' );

/**
 * Meta box: Outlier Landing Fields.
 */
function oc_add_landing_meta_box() {
	add_meta_box(
		'oc_landing_fields',
		__( 'Outlier Landing Fields', 'outlier-collective' ),
		'oc_render_landing_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'oc_add_landing_meta_box' );

/**
 * Text field helper.
 *
 * @param string $key   Meta key.
 * @param string $label Label.
 * @param string $value Value.
 * @param string $type  input type.
 */
function oc_field_text( $key, $label, $value, $type = 'text' ) {
	printf(
		'<p><label for="%1$s"><strong>%2$s</strong></label><br><input class="widefat" type="%3$s" id="%1$s" name="%1$s" value="%4$s"></p>',
		esc_attr( $key ),
		esc_html( $label ),
		esc_attr( $type ),
		esc_attr( $value )
	);
}

/**
 * Textarea helper.
 *
 * @param string $key   Meta key.
 * @param string $label Label.
 * @param string $value Value.
 * @param string $help  Optional help text.
 */
function oc_field_textarea( $key, $label, $value, $help = '' ) {
	printf(
		'<p><label for="%1$s"><strong>%2$s</strong></label><br><textarea class="widefat" rows="4" id="%1$s" name="%1$s">%3$s</textarea>',
		esc_attr( $key ),
		esc_html( $label ),
		esc_textarea( $value )
	);
	if ( $help ) {
		printf( '<span class="description">%s</span>', esc_html( $help ) );
	}
	echo '</p>';
}

/**
 * Image ID field with select button.
 *
 * @param string $key   Meta key.
 * @param string $label Label.
 * @param int    $id    Attachment ID.
 */
function oc_field_image( $key, $label, $id ) {
	$preview = $id ? wp_get_attachment_image( (int) $id, 'thumbnail' ) : '';
	printf(
		'<div class="oc-image-field" data-target="%1$s"><p><strong>%2$s</strong></p><input type="hidden" id="%1$s" name="%1$s" value="%3$s"><div class="oc-image-preview">%4$s</div><p><button type="button" class="button oc-upload">%5$s</button> <button type="button" class="button oc-remove">%6$s</button></p></div>',
		esc_attr( $key ),
		esc_html( $label ),
		esc_attr( (string) (int) $id ),
		$preview, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- image HTML from core.
		esc_html__( 'Select image', 'outlier-collective' ),
		esc_html__( 'Remove', 'outlier-collective' )
	);
}

/**
 * Render meta box markup.
 *
 * @param WP_Post $post Post object.
 */
function oc_render_landing_meta_box( $post ) {
	wp_nonce_field( 'oc_save_landing', 'oc_landing_nonce' );
	$d = oc_landing_default_meta();

	echo '<p class="description" style="margin-bottom:1.25em;">';
	esc_html_e( 'These fields power the “Outlier Landing” page template. Assign that template in Page → Template, then edit content here. Use the Media Library for all images.', 'outlier-collective' );
	echo '</p>';

	echo '<fieldset style="border:1px solid #ccd0d4;padding:12px 16px;margin-bottom:16px;"><legend><strong>' . esc_html__( 'Chapter 1 — Hero', 'outlier-collective' ) . '</strong></legend>';
	echo '<p class="description" style="margin-top:0;">' . esc_html__( 'Hero only: full-width, full-height background photo behind the headline, with a dark wash so type stays readable.', 'outlier-collective' ) . '</p>';
	oc_field_image( 'oc_hero_bg_id', __( 'Hero background image', 'outlier-collective' ), (int) get_post_meta( $post->ID, 'oc_hero_bg_id', true ) );
	oc_field_image(
		'oc_logo_white_id',
		__( 'Logo — white / reverse (hero on dark). Leave empty to use bundled Outlier Coaching SVG.', 'outlier-collective' ),
		(int) get_post_meta( $post->ID, 'oc_logo_white_id', true )
	);
	oc_field_image(
		'oc_logo_black_id',
		__( 'Logo — dark on light (light sections / cards). Leave empty to use bundled dark SVG.', 'outlier-collective' ),
		(int) get_post_meta( $post->ID, 'oc_logo_black_id', true )
	);
	oc_field_textarea(
		'oc_hero_headline',
		__( 'Hero headline (one line per line)', 'outlier-collective' ),
		oc_get_landing( $post->ID, 'oc_hero_headline', $d['oc_hero_headline'] )
	);
	oc_field_textarea( 'oc_hero_subhead', __( 'Hero subheadline', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_hero_subhead' ) );
	oc_field_text( 'oc_hero_cta_text', __( 'Hero button label', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_hero_cta_text' ) );
	oc_field_text( 'oc_hero_cta_url', __( 'Hero button URL', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_hero_cta_url' ), 'url' );
	echo '</fieldset>';

	echo '<fieldset style="border:1px solid #ccd0d4;padding:12px 16px;margin-bottom:16px;"><legend><strong>' . esc_html__( 'Chapter 2 — The problem', 'outlier-collective' ) . '</strong></legend>';
	oc_field_textarea( 'oc_problem_s1', __( 'Sentence 1', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_problem_s1' ) );
	oc_field_textarea( 'oc_problem_s2', __( 'Sentence 2', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_problem_s2' ) );
	oc_field_textarea( 'oc_problem_s3', __( 'Sentence 3', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_problem_s3' ) );
	oc_field_text(
		'oc_problem_orange_word',
		__( 'Orange accent — single word or short fragment (appears inside sentence 3)', 'outlier-collective' ),
		oc_get_landing( $post->ID, 'oc_problem_orange_word' )
	);
	echo '</fieldset>';

	echo '<fieldset style="border:1px solid #ccd0d4;padding:12px 16px;margin-bottom:16px;"><legend><strong>' . esc_html__( 'Chapter 3 — The path', 'outlier-collective' ) . '</strong></legend>';
	oc_field_textarea( 'oc_path_intro', __( 'Intro line (optional)', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_path_intro' ) );
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d step number */
		oc_field_text( "oc_step{$i}_label", sprintf( __( 'Step %d — label', 'outlier-collective' ), $i ), oc_get_landing( $post->ID, "oc_step{$i}_label" ) );
		oc_field_textarea( "oc_step{$i}_desc", sprintf( __( 'Step %d — description', 'outlier-collective' ), $i ), oc_get_landing( $post->ID, "oc_step{$i}_desc" ) );
	}
	echo '</fieldset>';

	echo '<fieldset style="border:1px solid #ccd0d4;padding:12px 16px;margin-bottom:16px;"><legend><strong>' . esc_html__( 'Chapter 4 — What this is (cards)', 'outlier-collective' ) . '</strong></legend>';
	$oc_offer_card_slots = array( 1, 2, 3, 5, 6 );
	foreach ( $oc_offer_card_slots as $oc_display_idx => $i ) {
		$oc_card_label_num = (int) $oc_display_idx + 1;
		echo '<h4 style="margin:12px 0 8px;">' . sprintf( esc_html__( 'Card %d', 'outlier-collective' ), $oc_card_label_num ) . '</h4>';
		oc_field_image( "oc_offer{$i}_img", __( 'Image', 'outlier-collective' ), (int) get_post_meta( $post->ID, "oc_offer{$i}_img", true ) );
		oc_field_text( "oc_offer{$i}_title", __( 'Title', 'outlier-collective' ), oc_get_landing( $post->ID, "oc_offer{$i}_title" ) );
		oc_field_text( "oc_offer{$i}_location", __( 'Location (optional — shows under title with pin)', 'outlier-collective' ), oc_get_landing( $post->ID, "oc_offer{$i}_location" ) );
		oc_field_textarea( "oc_offer{$i}_text", __( 'Description', 'outlier-collective' ), oc_get_landing( $post->ID, "oc_offer{$i}_text" ) );
	}
	oc_field_text(
		'oc_offerings_learn_more_url',
		__( '“Learn more” link for every card (default #contact — discovery / booking section). Use a full URL for Calendly etc.', 'outlier-collective' ),
		oc_get_landing( $post->ID, 'oc_offerings_learn_more_url' )
	);
	echo '</fieldset>';

	echo '<fieldset style="border:1px solid #ccd0d4;padding:12px 16px;margin-bottom:16px;"><legend><strong>' . esc_html__( 'Chapter 5 — Philosophy', 'outlier-collective' ) . '</strong></legend>';
	oc_field_textarea(
		'oc_philosophy_text',
		__( 'Body copy — one paragraph per block (separate with a blank line). Order sets scroll reveal.', 'outlier-collective' ),
		oc_get_landing( $post->ID, 'oc_philosophy_text' )
	);
	echo '</fieldset>';

	echo '<fieldset style="border:1px solid #ccd0d4;padding:12px 16px;margin-bottom:16px;"><legend><strong>' . esc_html__( 'Testimonials', 'outlier-collective' ) . '</strong></legend>';
	oc_field_text( 'oc_testimonials_label', __( 'Section label (eyebrow)', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_testimonials_label' ) );
	for ( $ti = 1; $ti <= 4; $ti++ ) {
		/* translators: %d testimonial number */
		oc_field_textarea( "oc_testimonial_{$ti}", sprintf( __( 'Testimonial %d', 'outlier-collective' ), $ti ), oc_get_landing( $post->ID, "oc_testimonial_{$ti}" ) );
	}
	echo '</fieldset>';

	echo '<fieldset style="border:1px solid #ccd0d4;padding:12px 16px;margin-bottom:16px;"><legend><strong>' . esc_html__( 'Upcoming talks (cards)', 'outlier-collective' ) . '</strong></legend>';
	oc_field_text( 'oc_upcoming_talks_label', __( 'Section label (eyebrow)', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_upcoming_talks_label' ) );
	for ( $ui = 1; $ui <= 4; $ui++ ) {
		echo '<p><strong>' . sprintf(
			/* translators: %d talk card number */
			esc_html__( 'Talk card %d', 'outlier-collective' ),
			$ui
		) . '</strong></p>';
		oc_field_text( "oc_utalk_{$ui}_title", __( 'Title (leave empty to hide this card)', 'outlier-collective' ), oc_get_landing( $post->ID, "oc_utalk_{$ui}_title" ) );
		oc_field_text( "oc_utalk_{$ui}_meta", __( 'Date / venue (one line)', 'outlier-collective' ), oc_get_landing( $post->ID, "oc_utalk_{$ui}_meta" ) );
		oc_field_textarea( "oc_utalk_{$ui}_desc", __( 'Short description (optional)', 'outlier-collective' ), oc_get_landing( $post->ID, "oc_utalk_{$ui}_desc" ) );
		oc_field_text( "oc_utalk_{$ui}_url", __( 'Link URL (optional — blank uses Contact section)', 'outlier-collective' ), oc_get_landing( $post->ID, "oc_utalk_{$ui}_url" ), 'url' );
	}
	echo '</fieldset>';

	echo '<fieldset style="border:1px solid #ccd0d4;padding:12px 16px;margin-bottom:16px;"><legend><strong>' . esc_html__( 'Chapter 6 — Talks', 'outlier-collective' ) . '</strong></legend>';
	oc_field_textarea( 'oc_talks_text', __( 'Headline copy', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_talks_text' ) );
	oc_field_text( 'oc_talks_cta_text', __( 'CTA label', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_talks_cta_text' ) );
	oc_field_text( 'oc_talks_cta_url', __( 'CTA URL', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_talks_cta_url' ), 'url' );
	echo '</fieldset>';

	echo '<fieldset style="border:1px solid #ccd0d4;padding:12px 16px;margin-bottom:16px;"><legend><strong>' . esc_html__( 'Chapter 7 — Contact', 'outlier-collective' ) . '</strong></legend>';
	oc_field_text( 'oc_contact_heading', __( 'Heading', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_contact_heading' ) );
	oc_field_textarea( 'oc_contact_body', __( 'Intro copy', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_contact_body' ) );
	oc_field_textarea( 'oc_confidentiality_text', __( 'Confidentiality statement (footer — flat band on cream)', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_confidentiality_text' ) );
	echo '<p><strong>' . esc_html__( 'Primary action (orange button on page)', 'outlier-collective' ) . '</strong></p>';
	oc_field_text( 'oc_calendly_label', __( 'Calendly: label', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_calendly_label' ) );
	oc_field_text( 'oc_calendly_url', __( 'Calendly: URL', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_calendly_url' ), 'url' );
	echo '<p><strong>' . esc_html__( 'Secondary action (outline button)', 'outlier-collective' ) . '</strong></p>';
	oc_field_text( 'oc_email_label', __( 'Email: label', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_email_label' ) );
	oc_field_text( 'oc_email_url', __( 'Email: URL (mailto:)', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_email_url' ), 'url' );
	echo '</fieldset>';

	echo '<fieldset style="border:1px solid #ccd0d4;padding:12px 16px;margin-bottom:16px;"><legend><strong>' . esc_html__( 'Footer', 'outlier-collective' ) . '</strong></legend>';
	oc_field_image(
		'oc_footer_logo_id',
		__( 'Footer logo (dark on cream — optional). Leave empty to use the dark lockup field or bundled dark SVG.', 'outlier-collective' ),
		(int) get_post_meta( $post->ID, 'oc_footer_logo_id', true )
	);
	oc_field_text( 'oc_footer_copy', __( 'Copyright line', 'outlier-collective' ), oc_get_landing( $post->ID, 'oc_footer_copy' ) );
	echo '</fieldset>';
}

/**
 * Save landing meta.
 *
 * @param int $post_id Post ID.
 */
function oc_save_landing_meta( $post_id ) {
	if ( ! isset( $_POST['oc_landing_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['oc_landing_nonce'] ) ), 'oc_save_landing' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	$url_keys = array(
		'oc_hero_cta_url',
		'oc_talks_cta_url',
		'oc_email_url',
		'oc_calendly_url',
		'oc_offer3_retreat_1_url',
		'oc_offer3_retreat_2_url',
		'oc_offer3_retreat_3_url',
		'oc_utalk_1_url',
		'oc_utalk_2_url',
		'oc_utalk_3_url',
		'oc_utalk_4_url',
	);
	$int_keys = oc_landing_integer_meta_keys();
	$keys     = oc_landing_meta_keys();
	foreach ( $keys as $key ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] );
		if ( in_array( $key, $int_keys, true ) ) {
			update_post_meta( $post_id, $key, absint( $raw ) );
			continue;
		}
		if ( in_array( $key, $url_keys, true ) ) {
			update_post_meta( $post_id, $key, esc_url_raw( $raw ) );
			continue;
		}
		update_post_meta( $post_id, $key, sanitize_textarea_field( $raw ) );
	}
}
add_action( 'save_post_page', 'oc_save_landing_meta' );
