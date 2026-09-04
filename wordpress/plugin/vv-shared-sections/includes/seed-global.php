<?php
/**
 * Content for the site-wide section, from the approved design.
 *
 * The intro, FAQ and contact block that appear above the footer on every
 * page. Used once, when the seeder finds no site-wide section already in
 * place; it never overwrites one that exists.
 *
 * @package VV_Shared_Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'title'  => 'Global Content Above Footer',
	'fields' => array(
		'intro_eyebrow'          => 'Why VV Glass',
		'intro_heading'          => 'Glass That Works Harder for Your Property',
		'intro_subheading'       => 'Built for the Way Your Space Is Used Every Day',
		'intro_body'             => '<p>Glass should do more than fill a frame. It should make your property safer, brighter, more practical, and more valuable.</p>
        <p>At VV Glass, we look beyond the immediate job and consider how each glass solution will perform in real life, from daily wear and changing weather to privacy, security, access, and presentation.</p>
        <p>Whether we are replacing a damaged window, installing a new shopfront, upgrading a glass fence, or creating a custom feature, our goal is to give you a result that feels right from the first day and continues to work well long after installation.</p>

          <h3>Practical Advice Before Precision Work</h3>
          <p>We understand that most customers do not deal with glass every day. That is why we take the time to explain your options clearly, without overcomplicating the process.</p>
          <p>We help you understand which type of glass suits your space, what will meet safety requirements, and what will give you the best balance of appearance, durability, and value.</p>
          <p>Our team brings the right mix of technical skill and practical thinking to every project. You receive measured advice, careful planning, and workmanship that respects your time, your property, and your budget.</p>

          <h3>A Clearer Finish, A Better Experience</h3>
          <p>When you choose VV Glass, you are not just booking a glazing service. You are choosing a team focused on making the whole experience smoother, safer, and more reassuring.</p>
          <p>From the first conversation to the final clean-up, we aim to keep communication simple, pricing transparent, and results consistently sharp.</p>
          <p>If you want glass that looks clean, performs reliably, and adds lasting value to your property, we are ready to help.</p>',
		'cta_label'              => 'Book Your Consultation Here',
		'cta_url'                => '#contact',
		'faq_eyebrow'            => 'Got Questions?',
		'faq_heading'            => 'Frequently Asked Questions',
		'faq_subheading'         => 'Everything you need to know about our glazing services. Can\'t find your answer? Get in touch below.',
		'faq_open_first'         => 0,
		'contact_eyebrow'        => 'Get In Touch',
		'contact_heading'        => 'Contact VV Glass',
		'contact_subheading'     => 'Reach out for a free quote or fast emergency response. We\'re here to help across Sydney & Wollongong.',
		'contact_form_heading'   => 'Get In Touch with VV Glass',
		'contact_form_sub'       => 'Tell us about your project and we\'ll come back with clear, practical advice.',
		'phone'                  => '0412 991 904',
		'phone_note'             => 'Available 24/7 for bookings, quotes & emergencies.',
		'email'                  => 'damian@vvglass.com.au',
		'email_note'             => 'We reply to enquiries within one business day.',
		'location_label'         => 'Based In',
		'location'               => 'Engadine, NSW 2233',
		'location_note'          => 'Servicing Sydney & Wollongong: CBD, North Shore, Eastern Suburbs, Sutherland Shire, Illawarra',
		'map_query'              => 'Engadine NSW 2233 Australia',
	),
	'faqs'   => array(
		array(
			'question' => 'What areas do you service?',
			'answer'   => '<p>We service Sydney, Wollongong, and surrounding areas, helping homeowners, businesses, builders, property managers, and industrial clients with dependable glass and glazing solutions. Whether you need glass installation, glass repair, window replacement, or glass fencing, we tailor our approach to suit your property.</p>
<p>Every site is different, so availability can depend on your location, access, urgency, and the type of glass required. The easiest way to begin is to contact us, share your project details, and book a consultation or site assessment.</p>
<p>From there, we can confirm the next steps, discuss practical options, explain likely timeframes, and provide clear advice before any work begins.</p>',
		),
		array(
			'question' => 'What glass services do you provide?',
			'answer'   => '<p>We provide a wide range of glazing services for residential, commercial, and industrial properties, including glass installation, glass repair, window replacement, custom glass, and glass fencing. Our team can assist with upgrades, urgent repairs, decorative features, and safety-focused glass solutions.</p>
<p>Our work commonly includes windows, doors, shower screens, splashbacks, balustrades, partitions, shopfronts, pool fencing, and custom architectural glass. By offering multiple glazing services, we make it easier for you to manage your project through one experienced local team.</p>
<p>Whether your job is small, complex, planned, or urgent, we focus on clear communication, careful workmanship, compliant materials, and a clean finish.</p>',
		),
		array(
			'question' => 'Can you help with both homes and businesses?',
			'answer'   => '<p>Yes. We work with homeowners, business owners, strata managers, builders, designers, and industrial operators. We understand that a family home, retail shopfront, office partition, apartment block, or facility upgrade each needs a different balance of safety, style, durability, and timing.</p>
<p>For homes, we often focus on comfort, natural light, privacy, security, and everyday usability. For businesses, priorities may include reduced downtime, public safety, compliance, energy efficiency, and a professional appearance for staff, customers, and visitors.</p>
<p>We tailor our recommendations to your property, the purpose of the glass, and your budget, helping your project move forward with confidence, clarity, and lasting value.</p>',
		),
		array(
			'question' => 'How does your process work?',
			'answer'   => '<p>Our process usually starts with your enquiry, followed by a discussion about the property, the type of glass you need, the issue being solved, and any safety, design, or access requirements. Where needed, we can arrange a site visit to measure and assess the area.</p>
<p>After the assessment, we provide practical advice and a clear quote outlining the proposed work, materials, and expected timeframe. This helps you understand what is involved before approving the project and reduces the chance of confusion later.</p>
<p>Once approved, our licensed glaziers complete the work with attention to safety, fit, finish, clean-up, and a secure result.</p>',
		),
		array(
			'question' => 'Do you provide emergency glass repairs?',
			'answer'   => '<p>Yes. We provide emergency glass repairs when broken, cracked, or shattered glass creates an immediate safety or security concern. This may include damaged windows, doors, shopfronts, panels, or other glazing that needs prompt attention to protect people, stock, equipment, or property.</p>
<p>Our emergency work focuses on fast response, safe handling, and practical temporary or permanent solutions depending on the damage and material availability. Our priority is to reduce risk, secure the area, and restore confidence as quickly as possible.</p>
<p>If the glass cannot be fully replaced immediately, we can recommend the safest next step and arrange follow-up work to complete the job properly.</p>',
		),
		array(
			'question' => 'Are your glass solutions compliant with Australian standards?',
			'answer'   => '<p>Yes. We place strong importance on safety, quality, and compliance across every glazing project. We use durable, certified glass solutions and work to Australian safety and building standards, especially for doors, windows, balustrades, pool fencing, shopfronts, partitions, and other high-use areas.</p>
<p>Compliant glazing is not only about meeting regulations. It also helps protect people, improve performance, and ensure the finished installation is suitable for its location, load, exposure, and daily use. Choosing the right glass type is essential.</p>
<p>During your consultation, we can explain suitable options, including toughened, laminated, insulated, frosted, or custom glass, so you can choose confidently for your project.</p>',
		),
		array(
			'question' => 'Will I receive a clear quote before work begins?',
			'answer'   => '<p>Yes. We believe in transparent pricing, honest advice, and clear communication before work starts. After learning about your project or completing a site assessment, we can provide a detailed quote that outlines the proposed solution, expected work, materials, and relevant timeframes.</p>
<p>This clear approach helps you compare options, understand the value of different glass types, and make decisions without pressure. It also reduces the risk of hidden costs, vague estimates, or misunderstandings during the project.</p>
<p>If your needs change, or hidden site conditions are discovered, we discuss your options before moving ahead, so you stay informed from start to finish.</p>',
		),
		array(
			'question' => 'Why should I choose VV Glass?',
			'answer'   => '<p>You should choose VV Glass if you want local experience, licensed and insured glaziers, reliable service, and workmanship that is handled with care. We work across glass installation, repair, replacement, and custom glazing, giving you one dependable contact for many project types.</p>
<p>Our clients value our practical advice, careful measurements, compliant materials, transparent quotes, and attention to detail from the first enquiry through to final clean-up. We also understand the importance of minimising disruption in homes, businesses, and shared spaces.</p>
<p>Whether your priority is safety, style, energy efficiency, privacy, or a fast repair, we deliver glass solutions built to perform.</p>',
		),
	),
);
