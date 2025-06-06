<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="76x76" href="favicon/76.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/16.png">
    <link rel="stylesheet" href="assets/style.css">
    <title>FoodWise</title>
    <style>
        /* Custom properties for glow colors */
        :root {
            --hero-glow: rgba(0, 255, 127, 0.2);
            --ai-glow: rgba(168, 85, 247, 0.2);
            --contact-glow: rgba(0, 255, 127, 0.2);
            --transition-duration: 0.8s;
        }

        /* Scrollbar styles */
        html::-webkit-scrollbar {
            width: 8px;
            background: transparent;
        }

        html::-webkit-scrollbar-track {
            background: transparent;
            box-shadow: none;
            border: none;
            margin: 0;
        }

        html::-webkit-scrollbar-thumb {
            background: #1e1e1e;
            border-radius: 1px;
            border: none;
        }

        html::-webkit-scrollbar-thumb:hover {
            background: #2a2a2a;
        }

        html {
            scrollbar-width: thin;
            scrollbar-color: #1e1e1e transparent;
        }

        /* General layout */
        body {
            margin: 0;
            box-sizing: border-box;
            background-color: #090c0a;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        .navbar {
            background-color: #090c0a;
            padding: 15px 20px;
            
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            
            top: 0;
            z-index: 1000;
        }

        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .navbar .login-btn {
            background-color: transparent;
            border: 1px solid #00FF7F;
            color: #00FF7F;
            padding: 6px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
        }

        /* Section transitions with smooth glow */
        section {
            position: relative;
            transition: background var(--transition-duration) ease;
        }

        .hero-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px;
            background: linear-gradient(to right, var(--hero-glow) 0%, rgba(0, 255, 127, 0.1) 50%, rgba(0, 255, 127, 0.02) 100%), #090c0a;
            min-height: 100vh;
        }

        .hero-text {
            text-align: center;
            padding: 0;
            margin-bottom: 20px;
        }

        .hero-text h1 {
            font-size: 32px;
            font-weight: bold;
            line-height: 1.2;
        }

        .hero-text h1 span {
            color: #00FF7F;
        }

        .hero-text p {
            font-size: 16px;
            margin: 15px 0;
            color: #a0a0a0;
        }

        .hero-text .btn-primary {
            background-color: #00FF7F;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 25px;
        }

        .hero-text .btn-outline-light {
            border: 1px solid #fff;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 25px;
            color: #fff;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .hero-stats div {
            text-align: center;
        }

        .hero-stats div h3 {
            font-size: 20px;
            margin-bottom: 8px;
            color: #00FF7F;
            font-weight: 600;
        }

        .hero-stats div p {
            color: #a0a0a0;
            font-size: 12px;
            margin-top: -4px;
        }

        .hero-image {
            position: relative;
            width: 100%;
            max-width: 500px;
        }

        .hero-image img {
            width: 100%;
            border-radius: 15px;
            display: block;
        }

        .hero-image::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, var(--hero-glow) 0%, rgba(0, 255, 127, 0.1) 20%, transparent 40%);
            border-radius: 15px;
            z-index: 1;
        }

        .revenue-growth {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px 15px;
            border-radius: 10px;
            z-index: 2;
            text-align: left;
        }

        .revenue-growth h3 {
            font-size: 18px;
            margin: 0;
            color: #00FF7F;
            font-weight: 700;
        }

        .revenue-growth p {
            color: #fff;
            font-size: 12px;
            margin: 0;
        }

        .features-section {
            padding: 30px 20px;
            text-align: center;
            background-color: #090c0a;
        }

        .features-section.ai-insights {
            background: linear-gradient(to right, var(--ai-glow) 0%, rgba(168, 85, 247, 0.1) 50%, rgba(168, 85, 247, 0) 100%), #090c0a;
        }

        .features-section.ai-insights .section-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--ai-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .features-section.ai-insights .section-icon i {
            color: #A855F7;
            width: 30px;
            height: 30px;
        }

        .features-section.ai-insights .section-header-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--ai-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .features-section.ai-insights .section-header-icon i {
            color: #A855F7;
            width: 40px !important;
            height: 40px !important;
        }

        .card-header-icon {
            width: 35px;
            height: 35px;
            color: #A855F7;
        }

        .features-section h2 {
            font-size: 28px;
            font-weight: bold;
            color: #fff;
        }

        .features-section p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #a0a0a0;
        }

        .features-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .feature-card {
            background-color: #141414;
            padding: 15px;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .feature-card.ai-card.demo-card {
            background-color: var(--ai-glow);
            border: 2px solid #A855F7;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -5px;
            width: 10px;
            height: 100%;
            z-index: 0;
        }

        .feature-card::after {
            content: '';
            position: absolute;
            top: -5px;
            left: 0;
            width: 100%;
            height: calc(100% + 10px);
            z-index: -1;
            border-radius: 10px;
        }

        .feature-card:nth-child(1)::before {
            background-color: #0EA5E9;
        }

        .feature-card:nth-child(1)::after {
            background: radial-gradient(circle, rgba(14, 165, 233, 0.2) 0%, transparent 70%);
        }

        .feature-card:nth-child(2)::before {
            background-color: #00FF7F;
        }

        .feature-card:nth-child(2)::after {
            background: radial-gradient(circle, var(--hero-glow) 0%, transparent 70%);
        }

        .feature-card:nth-child(3)::before {
            background-color: #F97316;
        }

        .feature-card:nth-child(3)::after {
            background: radial-gradient(circle, rgba(249, 115, 22, 0.2) 0%, transparent 70%);
        }

        .feature-card:nth-child(4)::before {
            background-color: #A855F7;
        }

        .feature-card:nth-child(4)::after {
            background: radial-gradient(circle, var(--ai-glow) 0%, transparent 70%);
        }

        .feature-card:nth-child(5)::before {
            background-color: #EC4899;
        }

        .feature-card:nth-child(5)::after {
            background: radial-gradient(circle, rgba(236, 72, 153, 0.2) 0%, transparent 70%);
        }

        .feature-card:nth-child(6)::before {
            background-color: #0EA5E9;
        }

        .feature-card:nth-child(6)::after {
            background: radial-gradient(circle, rgba(14, 165, 233, 0.2) 0%, transparent 70%);
        }

        .feature-card.ai-card::before {
            display: none;
        }

        .feature-card.ai-card::after {
            background: none;
        }

        .feature-card .card-body {
            position: relative;
            z-index: 1;
        }

        .feature-card h3 {
            font-size: 18px;
            margin: 10px 0;
            color: #fff;
            font-weight: 700;
        }

        .feature-card p {
            color: #a0a0a0;
            font-size: 14px;
        }

        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle.blue {
            background-color: rgba(14, 165, 233, 0.2);
        }

        .icon-circle.green {
            background-color: var(--hero-glow);
        }

        .icon-circle.orange {
            background-color: rgba(249, 115, 22, 0.2);
        }

        .icon-circle.purple {
            background-color: var(--ai-glow);
        }

        .icon-circle.pink {
            background-color: rgba(236, 72, 153, 0.2);
        }

        .icon-circle.ai-icon {
            background-color: var(--ai-glow);
        }

        .card-icon {
            width: 20px;
            height: 20px;
        }

        .card-icon.blue {
            color: #0EA5E9;
        }

        .card-icon.green {
            color: #00FF7F;
        }

        .card-icon.orange {
            color: #F97316;
        }

        .card-icon.purple {
            color: #A855F7;
        }

        .card-icon.pink {
            color: #EC4899;
        }

        .card-icon.ai-icon {
            color: #A855F7;
        }

        .demo-button {
            display: inline-block;
            background-color: transparent;
            border: 2px solid #A855F7;
            color: #A855F7;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
            transition: transform 0.2s ease;
        }

        .demo-button:hover {
            background-color: var(--ai-glow);
            transform: translateY(-2px);
        }

        .footer {
            padding: 30px 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            background-color: #090c0a;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, var(--contact-glow) 0%, transparent 70%);
            z-index: 0;
        }

        .footer-column {
            z-index: 1;
            flex: 1 1 100%;
            min-width: 200px;
        }

        .footer-column h4 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #fff;
            font-weight: bold;
        }

        .footer-column p {
            color: #a0a0a0;
            font-size: 12px;
        }

        .footer-column a {
            color: #a0a0a0;
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .footer-column a:hover {
            color: #00FF7F;
        }

        .footer-column .social-icons {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .footer-column .social-icons a {
            display: inline-block;
        }

        .footer-column .social-icons i {
            color: #a0a0a0;
            width: 18px;
            height: 18px;
        }

        .footer-column .social-icons i:hover {
            color: #00FF7F;
        }

        .footer-column.contact-column a {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .footer-column.contact-column i {
            color: #00FF7F;
            width: 14px;
            height: 14px;
        }

        .footer-column.contact-column a:hover i {
            color: #00FF7F;
        }

        .footer-bottom {
            text-align: center;
            padding: 15px 20px;
            font-size: 12px;
            background-color: #090c0a;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-bottom p {
            color: #a0a0a0;
            margin: 0;
            display: block;
            margin-bottom: 10px;
        }

        .footer-bottom a {
            color: #a0a0a0;
            text-decoration: none;
            margin: 0 8px;
            text-transform: uppercase;
        }

        .footer-bottom a:hover {
            color: #00FF7F;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--hero-glow);
            border: 2px solid #00FF7F;
            color: #00FF7F;
            padding: 8px 16px;
            font-size: 18px;
            border-radius: 50px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 700;
            min-width: 120px;
        }

        .btn-action:hover {
            background-color: var(--hero-glow) !important;
            border-color: #00FF7F !important;
            color: #00FF7F !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .btn-action:active {
            background-color: var(--hero-glow) !important;
            border-color: #00FF7F !important;
            color: #00FF7F !important;
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .btn-action .lucide-icon {
            margin-right: 6px;
            color: #00FF7F;
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .action-icon {
            width: 18px;
            height: 18px;
            margin-right: 6px;
            margin-bottom: 2px;
            stroke-width: 2.5;
        }

        .action-icon-app-banner {
            color: #A855F7 !important;
            margin-right: 6px;
            margin-bottom: 2px;
            stroke-width: 3;
        }

        .integration-banner {
            margin-top: 30px;
            background-color: var(--hero-glow);
            padding: 15px;
            border-radius: 10px;
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .integration-banner.ai-insights-banner {
            background-color: var(--ai-glow);
        }

        .integration-banner .banner-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .integration-banner p {
            font-size: 16px;
            color: #fff;
            margin: 0;
            text-align: center;
        }

        .integration-banner .action-icon {
            color: #00FF7F;
        }

        .integration-banner.ai-insights-banner .action-icon {
            color: #A855F7;
        }

        .features-banner {
            color: #00FF7F !important;
        }

        .features-banner.ai-insights-banner {
            color: #A855F7 !important;
        }

        .how-it-works-section {
            padding: 30px 20px;
            text-align: center;
            background-color: #090c0a;
        }

        .how-it-works-section h2 {
            font-size: 28px;
            font-weight: bold;
            color: #fff;
        }

        .how-it-works-section p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #a0a0a0;
        }

        .how-it-works-steps {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
        }

        .how-it-works-steps::before {
            content: '';
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: calc(100% - 60px);
            background: repeating-linear-gradient(to bottom,
                    #252826,
                    #252826 10px,
                    transparent 10px,
                    transparent 20px);
            z-index: 0;
        }

        .step {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .step-number {
            width: 25px;
            height: 25px;
            background-color: #00FF7F;
            color: #090c0a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            margin: 0 auto 15px;
        }

        .step-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--hero-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }

        .step-icon i {
            color: #00FF7F;
            width: 25px;
            height: 25px;
        }

        .step h3 {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 8px;
        }

        .step p {
            font-size: 14px;
            color: #a0a0a0;
        }

        .inner-step-icon {
            color: #00FF7F;
            width: 30px;
            height: 30px;
        }

        .pricing-section {
            padding: 30px 20px;
            text-align: center;
            background-color: #090c0a;
        }

        .pricing-section h2 {
            font-size: 28px;
            font-weight: bold;
            color: #fff;
        }

        .pricing-section p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #a0a0a0;
        }

        .pricing-toggle {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .pricing-toggle span {
            font-size: 14px;
            color: #a0a0a0;
        }

        .pricing-toggle .toggle {
            width: 50px;
            height: 25px;
            background-color: #00FF7F;
            border-radius: 15px;
            position: relative;
            cursor: pointer;
        }

        .pricing-toggle .toggle::before {
            content: '';
            position: absolute;
            width: 21px;
            height: 21px;
            background-color: #fff;
            border-radius: 50%;
            top: 2px;
            left: 2px;
            transition: transform 0.3s ease;
        }

        .pricing-toggle .toggle.yearly::before {
            transform: translateX(25px);
        }

        .pricing-grid {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .pricing-card {
            background-color: #141414;
            padding: 20px;
            border-radius: 10px;
            position: relative;
            width: 100%;
            max-width: 350px;
            text-align: left;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 400px;
            transition: transform 0.3s ease;
        }

        .pricing-card.starter {
            transform: none;
        }

        .pricing-card.starter::before {
            content: '';
            position: absolute;
            top: 0;
            left: -5px;
            width: 10px;
            height: 100%;
            background-color: #0EA5E9;
            border-radius: 10px 0 0 10px;
        }

        .pricing-card.starter::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.2) 0%, transparent 70%);
            z-index: -1;
            border-radius: 10px;
        }

        .pricing-card.professional {
            transform: none;
        }

        .pricing-card.professional::before {
            content: '';
            position: absolute;
            top: 0;
            left: -5px;
            width: 10px;
            height: 100%;
            background-color: #00FF7F;
            border-radius: 10px 0 0 10px;
        }

        .pricing-card.professional::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, var(--hero-glow) 0%, transparent 70%);
            z-index: -1;
            border-radius: 10px;
        }

        .pricing-card.enterprise {
            transform: none;
        }

        .pricing-card.enterprise::before {
            content: '';
            position: absolute;
            top: 0;
            left: -5px;
            width: 10px;
            height: 100%;
            background-color: #A855F7;
            border-radius: 10px 0 0 10px;
        }

        .pricing-card.enterprise::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, var(--ai-glow) 0%, transparent 70%);
            z-index: -1;
            border-radius: 10px;
        }

        .pricing-card .popular-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background-color: var(--hero-glow);
            color: #00FF7F;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        .pricing-card h3 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .pricing-card p.plan-description {
            font-size: 12px;
            color: #a0a0a0;
            margin-bottom: 15px;
        }

        .pricing-card .price {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .pricing-card .price span {
            font-size: 14px;
            color: #a0a0a0;
        }

        .pricing-card ul {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .pricing-card ul li {
            font-size: 14px;
            color: #a0a0a0;
            margin-bottom: 8px;
            position: relative;
            padding-left: 20px;
        }

        .pricing-card ul li::before {
            content: '✔';
            position: absolute;
            left: 0;
            color: #00FF7F;
            font-weight: bold;
        }

        .pricing-card .btn-get-started {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            font-size: 16px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .pricing-card.starter .btn-get-started {
            background-color: rgba(14, 165, 233, 0.2);
            border: 2px solid #0EA5E9;
            color: #0EA5E9;
        }

        .pricing-card.starter .btn-get-started:hover {
            background-color: rgba(14, 165, 233, 0.2) !important;
            border-color: #0EA5E9 !important;
            color: #0EA5E9 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(14, 165, 233, 0.3);
        }

        .pricing-card.starter .btn-get-started:active {
            background-color: rgba(14, 165, 233, 0.2) !important;
            border-color: #0EA5E9 !important;
            color: #0EA5E9 !important;
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(14, 165, 233, 0.3);
        }

        .pricing-card.professional .btn-get-started {
            background-color: var(--hero-glow);
            border: 2px solid #00FF7F;
            color: #00FF7F;
        }

        .pricing-card.professional .btn-get-started:hover {
            background-color: var(--hero-glow) !important;
            border-color: #00FF7F !important;
            color: #00FF7F !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .pricing-card.professional .btn-get-started:active {
            background-color: var(--hero-glow) !important;
            border-color: #00FF7F !important;
            color: #00FF7F !important;
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .pricing-card.enterprise .btn-get-started {
            background-color: var(--ai-glow);
            border: 2px solid #A855F7;
            color: #A855F7;
        }

        .pricing-card.enterprise .btn-get-started:hover {
            background-color: var(--ai-glow) !important;
            border-color: #A855F7 !important;
            color: #A855F7 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(168, 85, 247, 0.3);
        }

        .pricing-card.enterprise .btn-get-started:active {
            background-color: var(--ai-glow) !important;
            border-color: #A855F7 !important;
            color: #A855F7 !important;
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(168, 85, 247, 0.3);
        }

        .faq-section {
            padding: 30px 20px;
            text-align: center;
            background-color: #090c0a;
        }

        .faq-section h2 {
            font-size: 28px;
            font-weight: bold;
            color: #fff;
        }

        .faq-section p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #a0a0a0;
        }

        .accordion {
            max-width: 100 allocate;
            margin: 0 auto;
        }

        .accordion-item {
            background-color: #090c0a;
            border: none;
            border-bottom: 1px solid #a0a0a0;
            margin-bottom: 8px;
        }

        .accordion-button {
            background-color: #090c0a;
            color: #fff;
            font-size: 16px;
            padding: 12px;
            border: none;
            box-shadow: none !important;
            position: relative;
        }

        .accordion-button::after {
            filter: brightness(0) invert(1);
        }

        .accordion-button:not(.collapsed) {
            background-color: #090c0a;
            color: #fff;
        }

        .accordion-button:focus {
            box-shadow: none !important;
        }

        .accordion-body {
            background-color: #090c0a;
            color: #a0a0a0;
            font-size: 14px;
            padding: 12px;
            text-align: left;
        }

        .contact-section {
            padding: 30px 20px;
            text-align: center;
            background: linear-gradient(to top, var(--contact-glow) 0%, transparent 70%), #090c0a;
            position: relative;
            overflow: hidden;
        }

        .contact-section h2,
        .contact-section p,
        .contact-section .contact-card {
            position: relative;
            z-index: 1;
        }

        .contact-section h2 {
            font-size: 28px;
            font-weight: bold;
            color: #fff;
        }

        .contact-section p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #a0a0a0;
        }

        .contact-card {
            background-color: #141414;
            padding: 20px;
            border-radius: 10px;
            max-width: 100%;
            margin: 0 auto;
        }

        .contact-card .form-row {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 12px;
        }

        .contact-card .form-group {
            flex: 1;
        }

        .contact-card label {
            color: #a0a0a0;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 4px;
            display: block;
            text-align: left;
            padding-left: 5px;
        }

        .input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .contact-card input,
        .contact-card select,
        .contact-card textarea {
            background-color: #121212;
            border: 1px solid #444;
            color: #ffffff;
            padding: 8px 8px 8px 35px;
            border-radius: 8px;
            position: relative;
            z-index: 1;
            width: 100%;
            font-size: 14px;
        }

        .contact-card select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .contact-card input:focus,
        .contact-card select:focus,
        .contact-card textarea:focus {
            background-color: #121212;
            border-color: #34c759;
            box-shadow: none;
            color: #ffffff;
        }

        .contact-card input::placeholder,
        .contact-card select:invalid,
        .contact-card textarea::placeholder {
            color: #a0a0a0;
            opacity: 0.7;
        }

        .contact-card .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            color: #a0a0a0;
            pointer-events: none;
            width: 14px;
            height: 14px;
        }

        .contact-card textarea {
            height: 100px;
            resize: none;
            padding: 8px;
        }

        .contact-card .btn-send-message {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--hero-glow);
            border: 2px solid #00FF7F;
            color: #00FF7F;
            padding: 6px 12px;
            font-size: 16px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease;
            margin: 15px auto 0;
        }

        .contact-card .btn-send-message:hover {
            background-color: var(--hero-glow) !important;
            border-color: #00FF7F !important;
            color: #00FF7F !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .contact-card .btn-send-message:active {
            background-color: var(--hero-glow) !important;
            border-color: #00FF7F !important;
            color: #00FF7F !important;
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .contact-card .btn-send-message .lucide-icon {
            margin-right: 6px;
            color: #00FF7F;
            width: 18px;
            height: 18px;
        }

        /* Media Queries for Desktop */
        @media (min-width: 768px) {
            .navbar {
                padding: 20px 40px;
            }

            .navbar .logo {
                font-size: 24px;
            }

            .navbar .login-btn {
                padding: 8px 16px;
                font-size: 16px;
            }

            .hero-section {
                flex-direction: row;
                padding: 50px 40px;
            }

            .hero-text {
                flex: 1;
                text-align: left;
                padding-right: 30px;
                margin-bottom: 0;
            }

            .hero-text h1 {
                font-size: 48px;
            }

            .hero-text p {
                font-size: 18px;
            }

            .hero-text .btn-primary,
            .hero-text .btn-outline-light {
                padding: 12px 24px;
                font-size: 18px;
            }

            .hero-stats {
                justify-content: flex-start;
                gap: 30px;
            }

            .hero-stats div {
                text-align: left;
            }

            .hero-stats div h3 {
                font-size: 24px;
            }

            .hero-stats div p {
                font-size: 14px;
            }

            .hero-image {
                flex: 1;
                max-width: none;
            }

            .revenue-growth {
                bottom: 20px;
                right: 20px;
                padding: 15px 20px;
            }

            .revenue-growth h3 {
                font-size: 20px;
            }

            .revenue-growth p {
                font-size: 14px;
            }

            .features-section {
                padding: 50px 40px;
            }

            .features-section h2 {
                font-size: 36px;
            }

            .features-section p {
                font-size: 18px;
            }

            .features-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }

            .feature-card {
                padding: 20px;
                border-radius: 15px;
            }

            .feature-card h3 {
                font-size: 20px;
            }

            .feature-card p {
                font-size: 16px;
            }

            .icon-circle {
                width: 50px;
                height: 50px;
            }

            .card-icon {
                width: 24px;
                height: 24px;
            }

            .demo-button {
                padding: 10px 20px;
                margin-top: 20px;
            }

            .footer {
                padding: 50px 40px;
                flex-wrap: nowrap;
                gap: 0;
                justify-content: space-between;
            }

            .footer-column {
                flex: 1 1 auto;
                min-width: 150px;
            }

            .footer-column h4 {
                font-size: 18px;
                margin-bottom: 15px;
            }

            .footer-column p,
            .footer-column a {
                font-size: 14px;
            }

            .footer-column .social-icons i {
                width: 20px;
                height: 20px;
            }

            .footer-column.contact-column i {
                width: 16px;
                height: 16px;
            }

            .footer-bottom {
                text-align: left;
                padding: 20px 40px;
                font-size: 14px;
            }

            .footer-bottom p {
                display: inline;
                margin-bottom: 0;
            }

            .footer-bottom a {
                margin: 0 10px;
            }

            .btn-action {
                padding: 8px 16px;
                font-size: 18px;
            }

            .action-icon {
                width: 20px;
                height: 20px;
            }

            .integration-banner {
                margin-top: 40px;
                padding: 20px;
                border-radius: 15px;
                max-width: 800px;
                margin-left: auto;
                margin-right: auto;
                display: flex;
                justify-content: center;
            }

            .integration-banner .banner-content {
                justify-content: center;
                text-align: center;
            }

            .integration-banner p {
                font-size: 18px;
            }

            .how-it-works-section {
                padding: 50px 40px;
            }

            .how-it-works-section h2 {
                font-size: 36px;
            }

            .how-it-works-section p {
                font-size: 18px;
            }

            .how-it-works-steps {
                flex-direction: row;
                gap: 0;
            }

            .how-it-works-steps::before {
                top: 15px;
                left: 12%;
                width: 76%;
                height: 2px;
                background: repeating-linear-gradient(to right,
                        #252826,
                        #252826 10px,
                        transparent 10px,
                        transparent 20px);
                transform: none;
            }

            .step-number {
                width: 30px;
                height: 30px;
                font-size: 16px;
            }

            .step-icon {
                width: 80px;
                height: 80px;
            }

            .step-icon i {
                width: 30px;
                height: 30px;
            }

            .step h3 {
                font-size: 20px;
            }

            .step p {
                font-size: 16px;
            }

            .pricing-section {
                padding: 50px 40px;
            }

            .pricing-section h2 {
                font-size: 36px;
            }

            .pricing-section p {
                font-size: 18px;
            }

            .pricing-toggle {
                gap: 10px;
                margin-bottom: 30px;
            }

            .pricing-toggle span {
                font-size: 16px;
            }

            .pricing-toggle .toggle {
                width: 60px;
                height: 30px;
            }

            .pricing-toggle .toggle::before {
                width: 26px;
                height: 26px;
            }

            .pricing-toggle .toggle.yearly::before {
                transform: translateX(30px);
            }

            .pricing-grid {
                flex-direction: row;
                justify-content: center;
                gap: 20px;
            }

            .pricing-card {
                padding: 30px;
                border-radius: 15px;
                width: 300px;
                min-height: 450px;
            }

            .pricing-card.starter {
                transform: translateY(10px);
            }

            .pricing-card.professional {
                transform: translateY(-10px);
            }

            .pricing-card.enterprise {
                transform: translateY(10px);
            }

            .pricing-card h3 {
                font-size: 24px;
            }

            .pricing-card p.plan-description {
                font-size: 14px;
            }

            .pricing-card .price {
                font-size: 36px;
            }

            .pricing-card .price span {
                font-size: 16px;
            }

            .pricing-card ul li {
                font-size: 16px;
                padding-left: 25px;
            }

            .pricing-card .btn-get-started {
                padding: 8px 16px;
                font-size: 18px;
            }

            .faq-section {
                padding: 50px 40px;
            }

            .faq-section h2 {
                font-size: 36px;
            }

            .faq-section p {
                font-size: 18px;
            }

            .accordion {
                max-width: 800px;
            }

            .accordion-button {
                font-size: 18px;
                padding: 15px;
            }

            .accordion-body {
                font-size: 16px;
                padding: 15px;
            }

            .contact-section {
                padding: 50px 40px;
            }

            .contact-section h2 {
                font-size: 36px;
            }

            .contact-section p {
                font-size: 18px;
            }

            .contact-card {
                padding: 30px;
                border-radius: 15px;
                max-width: 800px;
            }

            .contact-card .form-row {
                flex-direction: row;
                gap: 20px;
                margin-bottom: 15px;
            }

            .contact-card label {
                font-size: 14px;
                margin-bottom: 5px;
            }

            .contact-card input,
            .contact-card select,
            .contact-card textarea {
                padding: 10px 10px 10px 40px;
                border-radius: 10px;
                font-size: 16px;
            }

            .contact-card .input-icon {
                left: 15px;
                width: 16px;
                height: 16px;
            }

            .contact-card textarea {
                height: 120px;
                padding: 10px;
            }

            .contact-card .btn-send-message {
                padding: 8px 16px;
                font-size: 18px;
                margin: 20px auto 0;
            }

            .contact-card .btn-send-message .lucide-icon {
                width: 20px;
                height: 20px;
            }
        }

        /* Additional Media Queries for Larger Screens */
        @media (min-width: 1200px) {
            .navbar {
                padding: 20px 60px;
            }

            .hero-section {
                padding: 60px 60px;
            }

            .features-section {
                padding: 60px 60px;
            }

            .how-it-works-section {
                padding: 60px 60px;
            }

            .pricing-section {
                padding: 60px 60px;
            }

            .faq-section {
                padding: 60px 60px;
            }

            .contact-section {
                padding: 60px 60px;
            }

            .footer {
                padding: 60px 60px;
            }
        }

        /* Media Query for Mobile to Remove Dashed Line */
        @media (max-width: 767.98px) {
            .how-it-works-steps::before {
                display: none;
            }
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            margin-right: 6px;
            margin-bottom: 4px;
            stroke-width: 2.2;
        }

        .mini-logo-icon {
            width: 24px;
            height: 24px;
            margin-right: 6px;
            margin-bottom: 4px;
            stroke-width: 2.2;
        }
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.359.0/dist/umd/lucide.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <nav class="navbar">
        <a class="logo"><i data-lucide="chef-hat" class="logo-icon"></i>FOODWISE</a>
        <a href="./login" class="btn btn-action"><i data-lucide="log-in" class="action-icon"></i>Login</a>
        <!--<a href="./dashboard" class="btn btn-action"><i data-lucide="layout-dashboard" class="action-icon"></i>Dashboard</a>-->
    </nav>

    <section class="hero-section">
        <div class="hero-text">
            <p style="font-size: 14px; color: #00FF7F;">SOFTWARE GESTIONALE PER RISTORANTI</p>
            <h1>Rivoluziona il tuo ristorante con <span>FOODWISE</span></h1>
            <p>La piattaforma intelligente tutto-in-uno che semplifica i processi, potenzia l’efficienza e aumenta i profitti
            attraverso analisi in tempo reale e automazione intelligente.</p>
            <div class="button-group">
                <button class="btn btn-action"><i data-lucide="sparkles" class="action-icon"></i>Inizia Ora</button>
                <button class="btn btn-action"><i data-lucide="flame" class="action-icon"></i>Richiedi una Demo</button>
            </div>
            <div class="hero-stats">
                <div>
                    <h3>500+</h3>
                    <p>Ristoranti</p>
                </div>
                <div>
                    <h3>25%</h3>
                    <p>Fatturato</p>
                </div>
                <div>
                    <h3>30min</h3>
                    <p>Durata dell’installazione</p>
                </div>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://www.smartworld.it/images/2023/02/27/scegliere-un-ristorante-3_crop_resize.jpg"
                alt="Restaurant Interior">
            <div class="revenue-growth">
                <h3>+25%</h3>
                <p>FATTURATO</p>
            </div>
        </div>
    </section>

    <section class="features-section">
        <br><br>
        <h2>FUNZIONALITA' AVANZATE</h2>
        <p>Tutto ciò di cui hai bisogno per gestire il tuo ristorante in modo efficiente, in un’unica piattaforma intelligente</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="card-body">
                    <div class="icon-circle blue">
                        <i data-lucide="bar-chart" class="card-icon blue"></i>
                    </div>
                    <h3>Analisi in tempo reale</h3>
                    <p>Tieni traccia di vendite, ricavi e tendenze dei clienti con dashboard e report intuitivi.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="card-body">
                    <div class="icon-circle green">
                        <i data-lucide="menu" class="card-icon green"></i>
                    </div>
                    <h3>Gestione avanzata del menù</h3>
                    <p>Aggiorna facilmente menù, prezzi e disponibilità. Crea piatti stagionali in pochi tocchi.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="card-body">
                    <div class="icon-circle orange">
                        <i data-lucide="users" class="card-icon orange"></i>
                    </div>
                    <h3>Personale e pianificazione</h3>
                    <p>Programma i turni, monitora le prestazioni e gestisci le buste paga, tutto in un’unica piattaforma.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="card-body">
                    <div class="icon-circle purple">
                        <i data-lucide="file-text" class="card-icon purple"></i>
                    </div>
                    <h3>Gestione degli ordini</h3>
                    <p>Prendi gli ordini direttamente al tavolo, inviali subito in cucina e riduci errori e tempi di attesa.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="card-body">
                    <div class="icon-circle pink">
                        <i data-lucide="calendar" class="card-icon pink"></i>
                    </div>
                    <h3>Sistema di prenotazioni</h3>
                    <p>Gestisci le prenotazioni, invia le conferme e ottimizza il ricambio dei tavoli.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="card-body">
                    <div class="icon-circle blue">
                        <i data-lucide="smartphone" class="card-icon blue"></i>
                    </div>
                    <h3>App mobile</h3>
                    <p>Offri ai tuoi clienti un’esperienza mobile personalizzata con il tuo brand per ordini e prenotazioni.</p>
                </div>
            </div>
        </div>
        <div class="integration-banner">
            <div class="banner-content">
                <i data-lucide="zap" class="action-icon"></i>
                <p class="features-banner"><b>FOODWISE</b> Si integra con il tuo POS esistente, i processori di pagamento e i servizi di consegna</p>
            </div>
        </div>
    </section>

    <section class="how-it-works-section">
        <br><br><br>
        <h2>COME FUNZIONA FOODWISE</h2>
        <p>Un semplice processo in quattro fasi per trasformare la gestione del tuo ristorante</p>
        <div class="how-it-works-steps">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-icon">
                    <i data-lucide="smartphone" class="inner-step-icon"></i>
                </div>
                <h3>CONFIGURA IL TUO RISTORANTE</h3>
                <p>Configura il tuo menù, i tavoli e il personale in pochi minuti con la nostra interfaccia intuitiva.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-icon">
                    <i data-lucide="file-text" class="inner-step-icon"></i>
                </div>
                <h3>GESTISCI GLI ORDINI</h3>
                <p>Prendi gli ordini direttamente al tavolo, inviali subito in cucina e monitora lo stato in tempo reale.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-icon">
                    <i data-lucide="bar-chart" class="inner-step-icon"></i>
                </div>
                <h3>MONITORA LE PRESTAZIONI</h3>
                <p>Monitora vendite, inventario e prestazioni del personale con analisi dettagliate.</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <div class="step-icon">
                    <i data-lucide="zap" class="inner-step-icon"></i>
                </div>
                <h3>OTTIMIZZA E CRESCI</h3>
                <p>Usa le informazioni generate dall’intelligenza artificiale per ottimizzare le operazioni e aumentare i ricavi.</p>
            </div>
        </div>
        <br><br><br>
    </section>

    <section class="features-section ai-insights">
        <div class="section-header-icon">
            <i data-lucide="brain" class="card-header-icon"></i>
        </div>
        <h2>ANALISI BASATE SULLA RACCOLTA DI DATI</h2>
        <p>FOODWISE analizza i dati per fornire indicazioni operative sull andamento del tuo locale.</p>
        <div class="features-grid">
            <div class="feature-card ai-card">
                <div class="card-body">
                    <div class="icon-circle ai-icon">
                        <i data-lucide="trending-up" class="card-icon purple"></i>
                    </div>
                    <h3>Analisi predittive</h3>
                    <p>Prevedi vendite, necessità di inventario e requisiti di personale basandoti su dati storici e tendenze, direttamente dal tuo dispositivo mobile.</p>
                </div>
            </div>
            <div class="feature-card ai-card">
                <div class="card-body">
                    <div class="icon-circle ai-icon">
                        <i data-lucide="users" class="card-icon purple"></i>
                    </div>
                    <h3>Analisi dei clienti</h3>
                    <p>Comprendi le preferenze e i comportamenti dei clienti per personalizzare marketing e offerte del menù, il tutto accessibile ovunque tu sia.</p>
                </div>
            </div>
            <div class="feature-card ai-card">
                <div class="card-body">
                    <div class="icon-circle ai-icon">
                        <i data-lucide="menu" class="card-icon purple"></i>
                    </div>
                    <h3>Ottimizzazione del menù</h3>
                    <p>Individua i piatti più redditizi del tuo menù e ricevi suggerimenti per migliorarli, direttamente dal tuo telefono.</p>
                </div>
            </div>
            <div class="feature-card ai-card">
                <div class="card-body">
                    <div class="icon-circle ai-icon">
                        <i data-lucide="shopping-cart" class="card-icon purple"></i>
                    </div>
                    <h3>Gestione intelligente dell’inventario</h3>
                    <p>Regola automaticamente gli ordini di inventario in base alla domanda prevista e riduci gli sprechi, con aggiornamenti in tempo reale sulla tua app mobile.</p>
                </div>
            </div>
            <div class="feature-card ai-card">
                <div class="card-body">
                    <div class="icon-circle ai-icon">
                        <i data-lucide="calendar" class="card-icon purple"></i>
                    </div>
                    <h3>Pianificazione ottimale</h3>
                    <p>Crea i turni del personale in base ai periodi di maggiore affluenza previsti e riduci i costi del lavoro, gestendo tutto comodamente dal tuo dispositivo mobile.</p>
                </div>
            </div>
            <div class="feature-card ai-card demo-card">
                <div class="card-body">
                    <div class="icon-circle ai-icon">
                        <i data-lucide="zap" class="card-icon purple"></i>
                    </div>
                    <h3>Guarda l’intelligenza artificiale in azione</h3>
                    <p>Prenota una demo personalizzata per scoprire come l’intelligenza artificiale di FOODWISE può trasformare la gestione del tuo ristorante.</p>
                    <a href="#" class="demo-button">Richiedi una demo dell’intelligenza artificiale</a>
                </div>
            </div>
        </div>
        <div class="integration-banner ai-insights-banner">
            <div class="banner-content">
                <i data-lucide="zap" class="action-icon-app-banner"></i>
                <p class="features-banner ai-insights-banner"><b>APP MOBILE FOODWISE</b> Si integra perfettamente con i sistemi esistenti del tuo ristorante per un’esperienza unificata.</p>
            </div>
        </div>
        <br><br>
    </section>

    <section class="pricing-section">
        <br><br>
        <h2>PREZZI SEMPLICI E TRASPARENTI</h2>
        <p>Scegli il piano che meglio si adatta alle esigenze del tuo ristorante.</p>
        <div class="pricing-toggle">
            <span>MENSILE</span>
            <div class="toggle"></div>
            <span>ANNUALE (20% in meno)</span>
        </div>
        <div class="pricing-grid">
            <div class="pricing-card starter">
                <h3>Base</h3>
                <p class="plan-description">IDEALE PER PICCOLI CAFFÈ E FOOD TRUCK.</p>
                <div class="price">49€ <span>/mese</span></div>
                <ul>
                    <li>Gestione del menù</li>
                    <li>Gestione dei tavoli</li>
                    <li>Reportistica di base</li>
                    <li>Fino a 3 account per il personale</li>
                    <li>Supporto via email</li>
                    <br><br><br>
                </ul>
                <a href="#" class="btn-get-started">Inizia ora</a>
            </div>
            <div class="pricing-card professional">
                <div class="popular-badge">PIÙ POPOLARE</div>
                <h3>Professionale</h3>
                <p class="plan-description">IDEALE PER RISTORANTI AFFERMATI</p>
                <div class="price">99€ <span>/mese</span></div>
                <ul>
                    <li>Tutto incluso nel piano Base</li>
                    <li>Gestione dell’inventario</li>
                    <li>Analisi avanzate</li>
                    <li>Account per il personale illimitati</li>
                    <li>Sistema di prenotazioni</li>
                    <li>Supporto prioritario</li>
                    <br><br>
                </ul>
                <a href="#" class="btn-get-started">Inizia ora</a>
            </div>
            <div class="pricing-card enterprise">
                <h3>Enterprise</h3>
                <p class="plan-description">PER CATENE DI RISTORANTI E FRANCHISING</p>
                <div class="price">199€ <span>/mese</span></div>
                <ul>
                    <li>Tutto incluso nel piano Professionale</li>
                    <li>Gestione multi-sede</li>
                    <li>Integrazioni personalizzate</li>
                    <li>Account manager dedicato</li>
                    <li>Accesso alle API</li>
                    <li>Opzione brandizzabile</li>
                    <li>Assistenza telefonica disponibile 24/7</li>
                </ul>
                <a href="#" class="btn-get-started">Inizia ora</a>
            </div>
        </div>
    </section>
    <br><br>

    <section class="faq-section">
        <h2>DOMANDE FREQUENTI</h2>
        <p>Hai domande? Abbiamo le risposte.</p>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        Quanto tempo ci vuole per configurare FOODWISE?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                    data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                    Configurare FOODWISE è rapido e semplice! La maggior parte dei ristoranti può essere operativa in meno di 30 minuti grazie al nostro processo di configurazione intuitivo.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Posso usare FOODWISE su più dispositivi?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                    Sì, FOODWISE è progettato per funzionare perfettamente su più dispositivi. Puoi accedervi da tablet, smartphone e desktop, garantendo flessibilità per il tuo team.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        FOODWISE si integra con il tuo sistema POS esistente?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                    Assolutamente! FOODWISE si integra con la maggior parte dei sistemi POS più diffusi, processori di pagamento e servizi di consegna per ottimizzare le tue operazioni.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        C'è un contratto o un impegno?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                    data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                    No, FOODWISE offre piani flessibili senza contratti a lungo termine. Puoi annullare in qualsiasi momento senza penali.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Quanto è sicura la mia attività di ristorazione e i dati che gestisce?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                    data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                    Prendiamo la sicurezza molto seriamente. FOODWISE utilizza la crittografia standard del settore e server sicuri per proteggere i dati del tuo ristorante.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-section">
    <br><br>
        <h2>Pronto a trasformare il tuo ristorante?</h2>
        <p>Contatta il nostro team per scoprire come FOODWISE può aiutare il tuo ristorante a prosperare.</p>
        <div class="contact-card">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nome</label>
                    <div class="input-wrapper">
                        <input type="text" id="name" placeholder="Il tuo nome" required>
                        <i data-lucide="user" class="input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" placeholder="nome@esempio.com" required>
                        <i data-lucide="mail" class="input-icon"></i>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Numero di telefono</label>
                    <div class="input-wrapper">
                        <input type="tel" id="phone" placeholder="ES: 0422634204" required>
                        <i data-lucide="phone" class="input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="restaurantName">Nome del ristorante</label>
                    <div class="input-wrapper">
                        <input type="text" id="restaurantName" placeholder="Il nome del tuo ristorante" required>
                        <i data-lucide="store" class="input-icon"></i>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="restaurantType">Tipo del ristorante</label>
                    <div class="input-wrapper">
                        <select id="restaurantType" required>
                            <option value="" disabled selected>Seleziona la tipologia del tuo ristorante</option>
                            <option value="cafe">Café</option>
                            <option value="casual">"Ristorante informale</option>
                            <option value="fine-dining">Alta ristorazione</option>
                            <option value="fast-food">Fast Food</option>
                            <option value="food-truck">Food Truck</option>
                            <option value="other">Altro</option>
                        </select>
                        <i data-lucide="chevron-down" class="input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="employees">Numero di dipendenti</label>
                    <div class="input-wrapper">
                        <input type="number" id="employees" placeholder="Numero di dipendenti" min="1" required>
                        <i data-lucide="users" class="input-icon"></i>
                    </div>
                </div>
            </div>
            <div>
                <label for="message">Messaggio</label>
                <textarea id="message" placeholder="Raccontaci del tuo ristorante e come possiamo aiutarti..."
                    required></textarea>
            </div>
            <button type="button" class="btn btn-send-message"
                onclick="alert('Messaggio inviato! Il nostro team ti risponderà al più presto.')">
                <i data-lucide="send" class="lucide-icon"></i> Send Message
            </button>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-column">
            <h4><i data-lucide="chef-hat" class="mini-logo-icon"></i>FOODWISE</h4>
            <p>Rivoluzionare la gestione dei ristoranti con soluzioni intelligenti.</p>
            <div class="social-icons">
                <a href="#"><i data-lucide="facebook"></i></a>
                <a href="#"><i data-lucide="twitter"></i></a>
                <a href="#"><i data-lucide="linkedin"></i></a>
                <a href="#"><i data-lucide="instagram"></i></a>
            </div>
        </div>
        <div class="footer-column">
            <h4>Prodotti</h4>
            <a href="#">Funzionalità</a>
            <a href="#">Prezzi</a>
            <a href="#">Integrazioni</a>
            <a href="#">Aggiornamenti</a>
        </div>
        <div class="footer-column">
            <h4>Azienda</h4>
            <a href="#">Chi siamo</a>
            <a href="#">Blog</a>
            <a href="#">Carriera</a>
            <a href="#">Stampa</a>
        </div>
        <div class="footer-column">
            <h4>Risorse</h4>
            <a href="#">Documentazione</a>
            <a href="#">Centro assistenza</a>
            <a href="#">Community</a>
            <a href="#">Webinars</a>
        </div>
        <div class="footer-column contact-column">
            <h4>Contact</h4>
            <a href="mailto:hello@foodwise.com"><i data-lucide="mail"></i>prova@foodwise.com</a>
            <a href="tel:+15551234567"><i data-lucide="phone"></i>+1 (555) 123-4567</a>
            <a href="#"><i data-lucide="map-pin"></i>Viale Rossi 42, Milano, Italia</a>
            <a href="http://www.foodwise.com"><i data-lucide="globe"></i>www.foodwise.com</a>
        </div>
    </footer>

    <div class="footer-bottom">
        <p>© 2025 FOODWISE. TUTTI I DIRITTI RISERVATI.</p>
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Cookie Policy</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof lucide !== 'undefined' && !window.lucideInitialized) {
                lucide.createIcons();
                window.lucideInitialized = true;
            }
        });

        const toggle = document.querySelector('.pricing-toggle .toggle');
        toggle.addEventListener('click', () => {
            toggle.classList.toggle('yearly');
            const prices = document.querySelectorAll('.pricing-card .price');
            if (toggle.classList.contains('yearly')) {
                prices[0].innerHTML = '39€ <span>/mese</span>';
                prices[1].innerHTML = '79€ <span>/mese</span>';
                prices[2].innerHTML = '159€ <span>/mese</span>';
            } else {
                prices[0].innerHTML = '49€ <span>/mese</span>';
                prices[1].innerHTML = '99€ <span>/mese</span>';
                prices[2].innerHTML = '199€ <span>/mese</span>';
            }
        });
    </script>
</body>

</html>