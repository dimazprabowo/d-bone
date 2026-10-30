    <style>
        @keyframes dashFadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes dashHeroShift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        @keyframes dashFloat { 0%, 100% { transform: translateY(0) rotate(-8deg); } 50% { transform: translateY(-12px) rotate(-8deg); } }
        .dash-enter { animation: dashFadeUp .55s cubic-bezier(.22,.9,.35,1) backwards; }
        .dash-hero { background-size: 200% auto; animation: dashHeroShift 9s ease-in-out infinite; }
        .dash-float { animation: dashFloat 6s ease-in-out infinite; }
        .dash-card { transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
        .dash-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgb(0 0 0 / .1), 0 4px 10px -3px rgb(0 0 0 / .08); }
        .dark .dash-card:hover { box-shadow: 0 10px 25px -5px rgb(0 0 0 / .45), 0 4px 10px -3px rgb(0 0 0 / .35); }
        @media (prefers-reduced-motion: reduce) {
            .dash-enter { opacity: 1; animation: none; }
            .dash-hero, .dash-float { animation: none; }
        }
    </style>
