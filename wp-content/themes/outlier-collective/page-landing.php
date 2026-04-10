<?php
/**
 * Template Name: Outlier Landing
 * Description: Flat, editorial, scroll-driven landing for Outlier Coaching.
 *
 * All copy and media are edited under “Outlier Landing Fields” on this page
 * (block editor compatible — the meta box appears below the editor).
 *
 * Bundled photography lives in assets/site/ (five fixed images). Upload replacements via
 * Outlier Landing Fields when you want different hero / offering / library images.
 *
 * @package Outlier_Collective
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

while ( have_posts() ) :
	the_post();
	$post_id = get_the_ID();

	$hero_img_id   = (int) oc_get_landing( $post_id, 'oc_hero_bg_id', 0 );
	$hero_bg_url   = $hero_img_id ? wp_get_attachment_image_url( $hero_img_id, 'full' ) : '';
	if ( ! $hero_bg_url ) {
		$hero_bg_url = oc_bundled_site_photo_url( 0 );
	}
	$logo_white_id  = (int) oc_get_landing( $post_id, 'oc_logo_white_id', 0 );
	$logo_black_id  = (int) oc_get_landing( $post_id, 'oc_logo_black_id', 0 );
	$footer_logo_id = (int) oc_get_landing( $post_id, 'oc_footer_logo_id', 0 );

	$bundled_logo_light = oc_bundled_logo_light_url();
	$bundled_logo_dark  = oc_bundled_logo_dark_url();

	$contact_conversation_bg_url = get_theme_file_uri( 'assets/contact-conversation-bg.png' );

	$headline_raw = oc_get_landing( $post_id, 'oc_hero_headline' );
	$head_lines   = array_filter( array_map( 'trim', explode( "\n", (string) $headline_raw ) ) );
	if ( empty( $head_lines ) ) {
		$defaults   = oc_landing_default_meta();
		$fallback   = isset( $defaults['oc_hero_headline'] ) ? trim( (string) $defaults['oc_hero_headline'] ) : '';
		$head_lines = $fallback !== '' ? array( $fallback ) : array( 'For those who want something different.' );
	}

	$oc_format_problem_s3 = static function ( $sentence, $accent ) {
		$sentence = (string) $sentence;
		$accent   = trim( (string) $accent );
		if ( $accent !== '' && $sentence !== '' && false !== strpos( $sentence, $accent ) ) {
			$parts = explode( $accent, $sentence, 2 );
			return esc_html( $parts[0] ) . '<span class="problem__accent">' . esc_html( $accent ) . '</span>' . esc_html( $parts[1] );
		}
		if ( $sentence !== '' && $accent !== '' ) {
			return esc_html( $sentence ) . ' <span class="problem__accent">' . esc_html( $accent ) . '</span>';
		}
		return esc_html( $sentence );
	};

	?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-oc-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<?php if ( $hero_bg_url ) : ?>
	<link rel="preload" as="image" href="<?php echo esc_url( $hero_bg_url ); ?>" fetchpriority="high">
	<?php endif; ?>
	<link rel="preload" as="script" href="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" crossorigin>
	<script>
		document.documentElement.classList.remove('no-oc-js');
		document.documentElement.classList.add('oc-js');
		document.documentElement.classList.add('oc-gsap-landing');
	</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'outlier-landing' ); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'outlier-collective' ); ?></a>

<div id="smooth-wrapper">
<div id="smooth-content">

<main id="main">

	<section class="chapter chapter--hero" id="top" data-oc-chapter="hero" aria-label="<?php esc_attr_e( 'Arrival', 'outlier-collective' ); ?>">
		<div class="hero__bg-wrap" aria-hidden="true">
			<?php if ( $hero_bg_url ) : ?>
				<div class="hero__bg-media">
					<img class="hero__bg-img" src="<?php echo esc_url( $hero_bg_url ); ?>" alt="" decoding="async" loading="eager" fetchpriority="high" />
				</div>
			<?php endif; ?>
			<div class="hero__bg-overlay"></div>
		</div>
		<div class="chapter__inner hero__grid">
			<div class="hero__text">
				<a class="hero__logo" href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php esc_attr_e( 'Outlier Coaching home', 'outlier-collective' ); ?>">
					<?php
					if ( $logo_white_id ) {
						echo wp_get_attachment_image(
							$logo_white_id,
							'medium',
							false,
							array(
								'alt'     => esc_attr__( 'Outlier Coaching', 'outlier-collective' ),
								'loading' => 'eager',
								'decoding' => 'async',
							)
						);
					} else {
						printf(
							'<img src="%1$s" alt="%2$s" class="hero__logo-img" width="260" height="114" loading="eager" decoding="async" fetchpriority="high" />',
							esc_url( $bundled_logo_light ),
							esc_attr__( 'Outlier Coaching', 'outlier-collective' )
						);
					}
					?>
				</a>
				<div class="hero__copy">
					<h1 class="hero__headline" id="oc-hero-headline">
						<?php
						$delay_index = 0;
						foreach ( $head_lines as $line ) {
							printf(
								'<span class="hero-line" style="transition-delay:%dms">%s</span>',
								esc_attr( (string) ( 120 + $delay_index * 220 ) ),
								oc_format_hero_accent_word( oc_soft_break_widow( $line ) )
							);
							$delay_index++;
						}
						?>
					</h1>
					<p class="hero__sub" id="oc-hero-sub"><?php echo esc_html( oc_soft_break_widow( oc_get_landing( $post_id, 'oc_hero_subhead' ) ) ); ?></p>
					<div class="hero__cta" id="oc-hero-cta">
						<a class="btn" href="<?php echo esc_url( oc_get_landing( $post_id, 'oc_hero_cta_url' ) ); ?>"><?php echo esc_html( oc_get_landing( $post_id, 'oc_hero_cta_text' ) ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="chapter chapter--problem" data-oc-chapter="problem" aria-label="<?php esc_attr_e( 'Recognition', 'outlier-collective' ); ?>">
		<div class="chapter__inner">
			<div class="problem__accent-dot" aria-hidden="true"></div>
			<div class="problem__text">
				<p data-oc-reveal><?php echo esc_html( oc_soft_break_widow( oc_get_landing( $post_id, 'oc_problem_s1' ) ) ); ?></p>
				<p data-oc-reveal><?php echo esc_html( oc_soft_break_widow( oc_get_landing( $post_id, 'oc_problem_s2' ) ) ); ?></p>
				<p data-oc-reveal><?php echo wp_kses( $oc_format_problem_s3( oc_soft_break_widow( oc_get_landing( $post_id, 'oc_problem_s3' ) ), oc_get_landing( $post_id, 'oc_problem_orange_word' ) ), array( 'span' => array( 'class' => true ) ) ); ?></p>
			</div>
		</div>
	</section>

	<section class="chapter chapter--path" data-oc-chapter="path" aria-label="<?php esc_attr_e( 'The path', 'outlier-collective' ); ?>">
		<div class="path-scroll-scene" data-oc-path-scroll-scene>
			<div class="path-scroll-sticky">
				<div class="chapter__inner path-journey__inner">
					<p class="chapter__label chapter__label--on-light" data-oc-reveal><?php esc_html_e( 'The path', 'outlier-collective' ); ?></p>
					<?php
					$path_intro = oc_get_landing( $post_id, 'oc_path_intro' );
					if ( $path_intro ) :
						?>
					<p class="path__intro" data-oc-path-intro><?php echo esc_html( oc_soft_break_widow( $path_intro ) ); ?></p>
					<?php endif; ?>

					<div class="path-journey__layout">
						<div class="path-journey__steps" role="list">
							<?php
							for ( $i = 1; $i <= 4; $i++ ) {
								$num = str_pad( (string) $i, 2, '0', STR_PAD_LEFT );
								?>
							<article class="path-step path-step--journey" data-oc-path-step role="listitem">
								<span class="path-step__num" aria-hidden="true"><?php echo esc_html( $num ); ?></span>
								<div class="path-step__body">
									<h2 class="path-step__label"><?php echo esc_html( oc_soft_break_widow( oc_get_landing( $post_id, "oc_step{$i}_label" ) ) ); ?></h2>
									<?php
									$step_desc = oc_get_landing( $post_id, "oc_step{$i}_desc" );
									if ( $step_desc !== '' && $step_desc !== null ) :
										?>
									<p class="path-step__desc"><?php echo esc_html( oc_soft_break_widow( $step_desc ) ); ?></p>
									<?php endif; ?>
								</div>
							</article>
								<?php
							}
							?>
						</div>

						<div class="path-journey__svg-wrap" aria-hidden="true">
							<svg class="path-journey__svg" viewBox="0 0 1000 168" preserveAspectRatio="xMidYMid meet" focusable="false">
								<path
									class="path-journey__trail-bg"
									d="M 28,118 C 168,78 288,138 420,102 S 652,128 788,96 S 912,112 972,104"
									fill="none"
									vector-effect="non-scaling-stroke"
								/>
								<path
									class="path-journey__trail-fg"
									d="M 28,118 C 168,78 288,138 420,102 S 652,128 788,96 S 912,112 972,104"
									fill="none"
									vector-effect="non-scaling-stroke"
								/>
								<g class="path-journey__markers"></g>
								<g class="path-journey__walker">
									<g class="path-journey__walker-bob" transform="scale(1.3)">
										<circle class="path-journey__walker-head" cx="0" cy="-15" r="5" fill="currentColor" />
										<path
											class="path-journey__walker-body"
											d="M0,-10 L0,3 M-4,11 L0,3 L5,10 M-5,0 L6,-2"
											fill="none"
											stroke="currentColor"
											stroke-width="1.85"
											stroke-linecap="round"
											stroke-linejoin="round"
										/>
									</g>
								</g>
							</svg>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="chapter chapter--talks" data-oc-chapter="talks" aria-label="<?php esc_attr_e( 'Talks', 'outlier-collective' ); ?>">
		<div class="chapter__inner talks__inner" data-oc-talks-inner>
			<h2 class="talks__headline" data-oc-talks-headline><?php echo oc_format_talks_headline_html( oc_get_landing( $post_id, 'oc_talks_text' ) ); ?></h2>
			<div class="talks__cta">
				<a class="btn btn--ghost-light" href="<?php echo esc_url( oc_get_landing( $post_id, 'oc_talks_cta_url' ) ); ?>"><?php echo esc_html( oc_get_landing( $post_id, 'oc_talks_cta_text' ) ); ?></a>
			</div>
		</div>
	</section>

	<section class="chapter chapter--offerings" data-oc-chapter="offerings" aria-label="<?php esc_attr_e( 'What this is', 'outlier-collective' ); ?>">
		<div class="chapter__inner">
			<p class="chapter__label chapter__label--on-stone" id="oc-offerings-label" data-oc-reveal><?php esc_html_e( 'What this is', 'outlier-collective' ); ?></p>
		</div>
		<div class="offerings__viewport" role="region" aria-labelledby="oc-offerings-label">
			<div id="oc-offerings-scroller" class="offerings__scroller" tabindex="0">
				<?php
				$oc_offer_learn_raw = trim( (string) oc_get_landing( $post_id, 'oc_offerings_learn_more_url' ) );
				if ( $oc_offer_learn_raw === '' ) {
					$oc_offer_learn_raw = '#contact';
				}
				$oc_offer_learn_href = ( 0 === strpos( $oc_offer_learn_raw, '#' ) ) ? esc_attr( $oc_offer_learn_raw ) : esc_url( $oc_offer_learn_raw );

				foreach ( array( 1, 2, 3, 5, 6 ) as $i ) {
					$img = (int) get_post_meta( $post_id, "oc_offer{$i}_img", true );

					$offer_title_plain    = oc_get_landing( $post_id, "oc_offer{$i}_title" );
					$offer_location_plain = trim( (string) oc_get_landing( $post_id, "oc_offer{$i}_location" ) );
					$offer_img_alt        = (string) $offer_title_plain;
					if ( '' !== $offer_location_plain ) {
						$offer_img_alt .= ', ' . $offer_location_plain;
					}
					$offer_body = oc_get_landing( $post_id, "oc_offer{$i}_text" );
					?>
				<article class="offering-card" data-oc-offering tabindex="0" role="group" aria-label="<?php echo esc_attr( $offer_title_plain ); ?>">
					<div class="offering-card__media">
						<?php
						if ( $img ) {
							echo wp_get_attachment_image(
								$img,
								'oc-offering',
								false,
								array(
									'class'    => 'offering-card__img',
									'alt'      => esc_attr( $offer_img_alt ),
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
						} else {
							$offer_demo = oc_bundled_site_photo_url( $i - 1 );
							if ( $offer_demo ) {
								printf(
									'<img class="offering-card__img" src="%1$s" alt="%2$s" width="960" height="540" loading="lazy" decoding="async" />',
									esc_url( $offer_demo ),
									esc_attr( $offer_img_alt )
								);
							} else {
								echo '<div class="offering-card__placeholder" role="img" aria-label="' . esc_attr__( 'Image placeholder', 'outlier-collective' ) . '"></div>';
							}
						}
						?>
					</div>
					<div class="offering-card__body">
						<h2 class="offering-card__title" title="<?php echo esc_attr( $offer_title_plain ); ?>"><?php echo esc_html( oc_soft_break_widow( $offer_title_plain ) ); ?></h2>
						<?php if ( '' !== $offer_location_plain ) : ?>
						<p class="offering-card__location">
							<span class="offering-card__location-pin"><?php echo oc_offering_location_pin_svg(); ?></span>
							<span class="offering-card__location-text"><?php echo esc_html( oc_soft_break_widow( $offer_location_plain ) ); ?></span>
						</p>
						<?php endif; ?>
						<div class="offering-card__desc"><?php echo oc_esc_html_with_br( $offer_body ); ?></div>
						<p class="offering-card__actions">
							<?php
							printf(
								'<a class="btn offering-card__btn" href="%1$s">%2$s</a>',
								$oc_offer_learn_href,
								esc_html__( 'Learn more', 'outlier-collective' )
							);
							?>
						</p>
					</div>
				</article>
					<?php
				}
				?>
			</div>
			<div class="offerings__nav" aria-label="<?php esc_attr_e( 'Offerings carousel', 'outlier-collective' ); ?>">
				<button type="button" class="offerings__arrow offerings__arrow--prev" id="oc-offerings-prev" aria-controls="oc-offerings-scroller" aria-label="<?php esc_attr_e( 'Previous offerings', 'outlier-collective' ); ?>">
					<svg class="offerings__arrow-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<button type="button" class="offerings__arrow offerings__arrow--next" id="oc-offerings-next" aria-controls="oc-offerings-scroller" aria-label="<?php esc_attr_e( 'Next offerings', 'outlier-collective' ); ?>">
					<svg class="offerings__arrow-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
			</div>
		</div>
	</section>

	<section id="philosophy" class="chapter chapter--philosophy" data-oc-chapter="philosophy" aria-label="<?php esc_attr_e( 'Philosophy', 'outlier-collective' ); ?>">
		<div class="chapter__inner">
			<div class="problem__accent-dot" aria-hidden="true"></div>
			<div class="problem__text">
				<?php
				$phil = oc_get_landing( $post_id, 'oc_philosophy_text' );
				$bits = array_filter( array_map( 'trim', preg_split( "/\n\s*\n/", $phil ) ) );
				foreach ( $bits as $chunk ) {
					echo '<p data-oc-reveal>' . esc_html( oc_soft_break_widow( $chunk ) ) . '</p>';
				}
				?>
			</div>
		</div>
	</section>

	<section class="chapter chapter--testimonials" data-oc-chapter="testimonials" aria-label="<?php esc_attr_e( 'Testimonials', 'outlier-collective' ); ?>">
		<div class="chapter__inner testimonials__inner">
			<p class="chapter__label chapter__label--on-stone" data-oc-reveal><?php echo esc_html( oc_soft_break_widow( oc_get_landing( $post_id, 'oc_testimonials_label' ) ) ); ?></p>
			<div class="testimonials__grid" role="list">
				<?php
				for ( $ti = 1; $ti <= 4; $ti++ ) {
					$quote = trim( (string) oc_get_landing( $post_id, "oc_testimonial_{$ti}" ) );
					if ( $quote === '' ) {
						continue;
					}
					?>
				<article class="testimonial-card" data-oc-testimonial role="listitem">
					<blockquote class="testimonial-card__quote">
						<?php
						$oc_tquote_img = oc_testimonial_quote_marks_url();
						if ( $oc_tquote_img !== '' ) :
							?>
						<span class="testimonial-card__mark" aria-hidden="true">
							<img class="testimonial-card__mark-img" src="<?php echo esc_url( $oc_tquote_img ); ?>" alt="" width="56" height="48" decoding="async" loading="lazy" />
						</span>
						<?php endif; ?>
						<p class="testimonial-card__text"><?php echo esc_html( oc_soft_break_widow( $quote ) ); ?></p>
					</blockquote>
				</article>
					<?php
				}
				?>
			</div>
		</div>
	</section>

	<?php
	$oc_upcoming_talks_visible = false;
	for ( $ui = 1; $ui <= 4; $ui++ ) {
		if ( trim( (string) oc_get_landing( $post_id, "oc_utalk_{$ui}_title" ) ) !== '' ) {
			$oc_upcoming_talks_visible = true;
			break;
		}
	}
	if ( $oc_upcoming_talks_visible ) :
		?>
	<section class="chapter chapter--upcoming-talks" data-oc-chapter="upcoming-talks" aria-label="<?php echo esc_attr( oc_get_landing( $post_id, 'oc_upcoming_talks_label' ) ); ?>">
		<div class="chapter__inner upcoming-talks__inner">
			<p class="chapter__label chapter__label--on-light" id="oc-upcoming-talks-label" data-oc-reveal><?php echo esc_html( oc_soft_break_widow( oc_get_landing( $post_id, 'oc_upcoming_talks_label' ) ) ); ?></p>
			<div class="upcoming-talks__grid" role="list" aria-labelledby="oc-upcoming-talks-label">
				<?php
				for ( $ui = 1; $ui <= 4; $ui++ ) {
					$ut_title = trim( (string) oc_get_landing( $post_id, "oc_utalk_{$ui}_title" ) );
					if ( '' === $ut_title ) {
						continue;
					}
					$ut_meta = trim( (string) oc_get_landing( $post_id, "oc_utalk_{$ui}_meta" ) );
					$ut_desc = trim( (string) oc_get_landing( $post_id, "oc_utalk_{$ui}_desc" ) );
					$ut_url  = trim( (string) oc_get_landing( $post_id, "oc_utalk_{$ui}_url" ) );
					$ut_href = '' !== $ut_url ? $ut_url : home_url( '/' ) . '#contact';
					$ut_link_external = false;
					if ( preg_match( '#^https?://#i', $ut_href ) ) {
						$ut_host = wp_parse_url( $ut_href, PHP_URL_HOST );
						$site_h  = wp_parse_url( home_url(), PHP_URL_HOST );
						if ( $ut_host && $site_h && strtolower( (string) $ut_host ) !== strtolower( (string) $site_h ) ) {
							$ut_link_external = true;
						}
					}
					?>
				<article class="upcoming-talk-card" data-oc-upcoming-talk role="listitem">
					<h3 class="upcoming-talk-card__title"><?php echo esc_html( oc_soft_break_widow( $ut_title ) ); ?></h3>
					<?php if ( '' !== $ut_meta ) : ?>
					<p class="upcoming-talk-card__meta"><?php echo esc_html( oc_soft_break_widow( $ut_meta ) ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $ut_desc ) : ?>
					<p class="upcoming-talk-card__desc"><?php echo esc_html( oc_soft_break_widow( $ut_desc ) ); ?></p>
					<?php endif; ?>
					<p class="upcoming-talk-card__action">
						<a class="btn btn--outline upcoming-talk-card__btn" href="<?php echo esc_url( $ut_href ); ?>"
						<?php
						if ( $ut_link_external ) {
							echo ' target="_blank" rel="noopener noreferrer"';
						}
						?>
						><?php esc_html_e( 'Find out more', 'outlier-collective' ); ?></a>
					</p>
				</article>
					<?php
				}
				?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<section class="chapter chapter--contact" id="contact" data-oc-chapter="contact" aria-label="<?php esc_attr_e( 'Contact', 'outlier-collective' ); ?>">
		<div class="contact__bg-wrap" aria-hidden="true">
			<div class="contact__bg-media">
				<img
					class="contact__bg-img"
					src="<?php echo esc_url( $contact_conversation_bg_url ); ?>"
					alt=""
					width="1920"
					height="1080"
					decoding="async"
					loading="lazy"
				/>
			</div>
			<div class="contact__bg-overlay"></div>
		</div>
		<div class="chapter__inner contact__inner">
			<h2 class="contact__heading" data-oc-contact-head><?php echo esc_html( oc_soft_break_widow( oc_get_landing( $post_id, 'oc_contact_heading' ) ) ); ?></h2>
			<p class="contact__lede" data-oc-contact-lede><?php
				$oc_contact_lede = (string) oc_get_landing( $post_id, 'oc_contact_body' );
				/* Keep “Just a conversation” from splitting across lines (NBSP ties the phrase). */
				$oc_contact_lede = str_replace( 'Just a conversation', "Just\xc2\xa0a\xc2\xa0conversation", $oc_contact_lede );
				echo esc_html( oc_soft_break_widow( $oc_contact_lede ) );
			?></p>

			<div class="contact__actions">
				<a class="btn contact__action contact__action--primary" data-oc-contact-btn href="<?php echo esc_url( oc_get_landing( $post_id, 'oc_calendly_url' ) ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html( oc_get_landing( $post_id, 'oc_calendly_label' ) ); ?></a>
				<a class="btn btn--outline contact__action" data-oc-contact-btn href="<?php echo esc_url( oc_get_landing( $post_id, 'oc_email_url' ) ); ?>"><?php echo esc_html( oc_get_landing( $post_id, 'oc_email_label' ) ); ?></a>
			</div>
		</div>
	</section>

	<footer class="site-footer" data-oc-chapter="footer">
		<div class="site-footer__inner">
			<div class="site-footer__brand">
				<div class="site-footer__logo">
					<?php
					if ( $footer_logo_id ) {
						echo wp_get_attachment_image(
							$footer_logo_id,
							'medium',
							false,
							array(
								'class'    => 'site-footer__logo-img',
								'alt'      => esc_attr__( 'Outlier Coaching', 'outlier-collective' ),
								'loading'  => 'lazy',
								'decoding' => 'async',
							)
						);
					} elseif ( $logo_black_id ) {
						echo wp_get_attachment_image(
							$logo_black_id,
							'medium',
							false,
							array(
								'class'    => 'site-footer__logo-img',
								'alt'      => esc_attr__( 'Outlier Coaching', 'outlier-collective' ),
								'loading'  => 'lazy',
								'decoding' => 'async',
							)
						);
					} else {
						printf(
							'<img src="%1$s" alt="%2$s" class="site-footer__logo-img" width="200" height="88" loading="lazy" decoding="async" />',
							esc_url( $bundled_logo_dark ),
							esc_attr__( 'Outlier Coaching', 'outlier-collective' )
						);
					}
					?>
				</div>
				<p class="site-footer__copy"><?php echo esc_html( oc_soft_break_widow( oc_get_landing( $post_id, 'oc_footer_copy' ) ) ); ?></p>
			</div>
			<?php
			$oc_confidential = trim( (string) oc_get_landing( $post_id, 'oc_confidentiality_text' ) );
			if ( '' !== $oc_confidential ) :
				?>
			<div class="site-footer__statement">
				<p class="site-footer__confidential" data-oc-footer-confidential><?php echo esc_html( oc_soft_break_widow( $oc_confidential ) ); ?></p>
			</div>
			<?php endif; ?>
		</div>
	</footer>

</main>

</div>
</div>

<div class="custom-cursor" id="oc-cursor" aria-hidden="true"><span class="custom-cursor__inner"></span></div>
<?php wp_footer(); ?>
</body>
</html>
	<?php
endwhile;
