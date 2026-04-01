<?php
/**
 * Default Menu Data Structure
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get default menu data
 */
function psmm_get_default_menu_data() {
    return array(
        'screens' => array(
            'label' => 'Screens',
            'has_dropdown' => true,
            'columns' => array(
                array(
                    'title' => 'By Application',
                    'items' => array(
                        array(
                            'label' => 'Rooftop & Mechanical Equipment',
                            'url' => '#',
                            'description' => 'HVAC, chillers, generators'
                        ),
                        array(
                            'label' => 'Parking Garages',
                            'url' => '#',
                            'description' => 'Multi-level structures, ventilation'
                        ),
                        array(
                            'label' => 'Ground Level & Landscape',
                            'url' => '#',
                            'description' => 'Utility areas, transformers'
                        ),
                        array(
                            'label' => 'Playgrounds & Recreation',
                            'url' => '#',
                            'description' => 'Schools, parks, daycare facilities'
                        ),
                    ),
                ),
                array(
                    'title' => 'By Performance',
                    'items' => array(
                        array(
                            'label' => 'Crash Rated Screens',
                            'url' => '#',
                            'description' => 'K4, K8, K12 certified barriers'
                        ),
                        array(
                            'label' => 'Acoustic Screens',
                            'url' => '#',
                            'description' => 'Sound reduction & noise control'
                        ),
                        array(
                            'label' => 'Vision Screens',
                            'url' => '#',
                            'description' => 'Standard architectural screening'
                        ),
                    ),
                ),
                array(
                    'title' => 'By Style',
                    'items' => array(
                        array('label' => 'Louver Patterns', 'url' => '#'),
                        array('label' => 'Perforated Patterns', 'url' => '#'),
                        array('label' => 'Corrugated Patterns', 'url' => '#'),
                        array('label' => 'Custom Designs', 'url' => '#'),
                    ),
                    'quick_links' => array(
                        array('label' => 'View All Screens', 'url' => '#', 'arrow' => true),
                        array('label' => 'Color Options', 'url' => '#'),
                    ),
                ),
            ),
            'featured' => array(
                'title' => 'Not Sure What You Need?',
                'text' => 'Answer 3 quick questions and we\'ll recommend the perfect screening solution for your project.',
                'cta_label' => 'Help Me Choose →',
                'cta_url' => '#',
            ),
        ),
        'enclosures' => array(
            'label' => 'Enclosures',
            'has_dropdown' => true,
            'columns' => array(
                array(
                    'title' => 'Enclosure Types',
                    'items' => array(
                        array(
                            'label' => 'Dumpster Enclosures',
                            'url' => '#',
                            'description' => 'Commercial waste containment solutions'
                        ),
                        array(
                            'label' => 'Rooftop Enclosures',
                            'url' => '#',
                            'description' => 'Equipment screening for rooftop installations'
                        ),
                        array(
                            'label' => 'Pedestrian Surround Enclosures',
                            'url' => '#',
                            'description' => 'Protective screening for pedestrian areas'
                        ),
                    ),
                    'view_all' => array('label' => 'View All Enclosures →', 'url' => '#'),
                ),
            ),
            'featured' => array(
                'title' => 'Enclosure Solutions',
                'text' => 'From architect-driven projects to municipal applications, our enclosures provide scalable solutions for any site requirement.',
                'cta_label' => 'Compare All Options →',
                'cta_url' => '#',
            ),
        ),
        'amenities' => array(
            'label' => 'Site Amenities',
            'has_dropdown' => true,
            'columns' => array(
                array(
                    'title' => 'Railings',
                    'items' => array(
                        array(
                            'label' => 'Architectural Railings',
                            'url' => '#',
                            'description' => 'Commercial & residential applications'
                        ),
                        array(
                            'label' => 'ADA Compliant Railings',
                            'url' => '#',
                            'description' => 'Code-compliant accessibility solutions'
                        ),
                    ),
                    'view_all' => array('label' => 'View All Railings →', 'url' => '#'),
                ),
                array(
                    'title' => 'Bollards',
                    'items' => array(
                        array(
                            'label' => 'Decorative Bollards',
                            'url' => '#',
                            'description' => 'Aesthetic site protection'
                        ),
                        array(
                            'label' => 'Security Bollards',
                            'url' => '#',
                            'description' => 'High-impact vehicle barriers'
                        ),
                        array(
                            'label' => 'Removable Bollards',
                            'url' => '#',
                            'description' => 'Flexible access control'
                        ),
                    ),
                    'view_all' => array('label' => 'View All Bollards →', 'url' => '#'),
                ),
                array(
                    'title' => 'Shades',
                    'items' => array(
                        array(
                            'label' => 'Canopy Shade Systems',
                            'url' => '#',
                            'description' => 'Large-scale coverage solutions'
                        ),
                        array(
                            'label' => 'Pergola Shades',
                            'url' => '#',
                            'description' => 'Architectural shade structures'
                        ),
                        array(
                            'label' => 'Custom Shade Solutions',
                            'url' => '#',
                            'description' => 'Project-specific designs'
                        ),
                    ),
                    'view_all' => array('label' => 'View All Shades →', 'url' => '#'),
                ),
            ),
            'featured' => array(
                'title' => 'Complete Your Project',
                'text' => 'Our site amenities integrate seamlessly with PalmSHIELD screening systems for a unified architectural appearance.',
                'cta_label' => 'Request Consultation →',
                'cta_url' => '#',
            ),
        ),
        'gates' => array(
            'label' => 'Gates',
            'has_dropdown' => true,
            'columns' => array(
                array(
                    'title' => 'Access Control+',
                    'items' => array(
                        array(
                            'label' => 'Complete Slide Gate Packages',
                            'url' => '#',
                            'description' => 'Turnkey automated gate systems with operators'
                        ),
                        array(
                            'label' => 'Single Track Systems',
                            'url' => '#',
                            'description' => 'Standard cantilever slide gates'
                        ),
                        array(
                            'label' => 'Double Track Systems',
                            'url' => '#',
                            'description' => 'Bi-parting slide gate configurations'
                        ),
                        array(
                            'label' => 'Gate Automation Kits',
                            'url' => '#',
                            'description' => 'Operators, photo eyes, keypads & more'
                        ),
                    ),
                    'view_all' => array('label' => 'View Access Control+ →', 'url' => '#'),
                ),
                array(
                    'title' => 'Architectural Gates',
                    'items' => array(
                        array(
                            'label' => 'Single Slide Gates',
                            'url' => '#',
                            'description' => 'Industrial strength, architectural flair'
                        ),
                        array(
                            'label' => 'Swing Gates',
                            'url' => '#',
                            'description' => 'Single & double leaf configurations'
                        ),
                        array(
                            'label' => 'Man Gates',
                            'url' => '#',
                            'description' => 'Personnel entry points'
                        ),
                        array(
                            'label' => 'Swing Man Gates',
                            'url' => '#',
                            'description' => 'Walk gate with swing operation'
                        ),
                    ),
                    'view_all' => array('label' => 'View Architectural Gates →', 'url' => '#'),
                ),
                array(
                    'title' => 'Vulcan Pedestrian Gates',
                    'items' => array(
                        array(
                            'label' => 'Wire Mesh Systems',
                            'url' => '#',
                            'description' => 'Full gate & surround packages'
                        ),
                        array(
                            'label' => 'Picket Solid Systems',
                            'url' => '#',
                            'description' => 'Privacy-focused pedestrian entry'
                        ),
                        array(
                            'label' => 'Ornamental Systems',
                            'url' => '#',
                            'description' => 'Decorative high-traffic access'
                        ),
                        array(
                            'label' => 'Bar Grating Systems',
                            'url' => '#',
                            'description' => 'Industrial-grade pedestrian gates'
                        ),
                    ),
                    'view_all' => array('label' => 'View Pedestrian Gates →', 'url' => '#'),
                ),
            ),
            'hardware' => array(
                'title' => 'Hardware',
                'items' => array(
                    array('label' => 'Gate Operators', 'url' => '#'),
                    array('label' => 'Hinges & Latches', 'url' => '#'),
                    array('label' => 'Access Controls', 'url' => '#'),
                    array('label' => 'Safety Devices', 'url' => '#'),
                ),
                'featured' => array(
                    'title' => 'Architect Resources',
                    'text' => 'CAD drawings, specs & BIM models for chain link and ornamental gate systems.',
                    'cta_label' => 'Download Resources →',
                    'cta_url' => '#',
                ),
            ),
        ),
        'resources' => array(
            'label' => 'Resources',
            'has_dropdown' => true,
            'columns' => array(
                array(
                    'title' => 'Technical Resources',
                    'items' => array(
                        array(
                            'label' => 'CAD Drawings',
                            'url' => '#',
                            'description' => 'DWG & PDF downloads'
                        ),
                        array(
                            'label' => 'Specifications',
                            'url' => '#',
                            'description' => 'CSI 3-part specs'
                        ),
                        array(
                            'label' => 'BIM Models',
                            'url' => '#',
                            'description' => 'Revit families'
                        ),
                        array(
                            'label' => 'Installation Guides',
                            'url' => '#',
                            'description' => 'Step-by-step instructions'
                        ),
                    ),
                ),
                array(
                    'title' => 'Sales Resources',
                    'items' => array(
                        array(
                            'label' => 'Product Brochures',
                            'url' => '#',
                            'description' => 'Downloadable PDFs'
                        ),
                        array(
                            'label' => 'Color Charts',
                            'url' => '#',
                            'description' => 'Standard & custom colors'
                        ),
                        array(
                            'label' => 'Project Gallery',
                            'url' => '#',
                            'description' => 'Completed installations'
                        ),
                        array(
                            'label' => 'Capabilities Statement',
                            'url' => '#',
                            'description' => 'Custom Metal Solutions'
                        ),
                    ),
                ),
                array(
                    'title' => 'Support',
                    'items' => array(
                        array(
                            'label' => 'FAQs',
                            'url' => '#',
                            'description' => 'Common questions answered'
                        ),
                        array(
                            'label' => 'Warranty Information',
                            'url' => '#',
                            'description' => 'Coverage details'
                        ),
                        array(
                            'label' => 'Find a Rep',
                            'url' => '#',
                            'description' => 'Local representation'
                        ),
                        array(
                            'label' => 'Contact Us',
                            'url' => '#',
                            'description' => 'Get in touch'
                        ),
                    ),
                ),
            ),
            'featured' => array(
                'title' => 'Request a Quote',
                'text' => 'Ready to start your project? Get a custom quote from our team with specifications tailored to your requirements.',
                'cta_label' => 'Get a Quote →',
                'cta_url' => '#',
            ),
        ),
    );
}

/**
 * Get current menu data (from options or default)
 */
function psmm_get_menu_data() {
    $saved_data = get_option('psmm_menu_data');
    if ($saved_data && is_array($saved_data)) {
        return $saved_data;
    }
    return psmm_get_default_menu_data();
}
