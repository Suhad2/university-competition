<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>University Competition - Live View</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root {
	--primary: #116967;
	--primary-light: #1a8a87;
	--primary-dark: #0d5452;
	--primary-darker: #094442;
	--accent: #2dd4bf;
	--accent-light: #5eead4;
	--gradient-primary: linear-gradient(135deg, #116967 0%, #0d5452 50%, #094442 100%);
	--gradient-accent: linear-gradient(135deg, #2dd4bf 0%, #14b8a6 50%, #0d9488 100%);
	--gradient-dark: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
	--gradient-glass: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
	--shadow-sm: 0 4px 6px -1px rgba(17, 105, 103, 0.1), 0 2px 4px -1px rgba(17, 105, 103, 0.06);
	--shadow-md: 0 10px 15px -3px rgba(17, 105, 103, 0.15), 0 4px 6px -2px rgba(17, 105, 103, 0.1);
	--shadow-lg: 0 20px 25px -5px rgba(17, 105, 103, 0.2), 0 10px 10px -5px rgba(17, 105, 103, 0.1);
	--shadow-glow: 0 0 40px rgba(45, 212, 191, 0.3);
}

* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

html,
body {
	height: 100vh;
	overflow: hidden;
	font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	background: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #0d5452 50%, #116967 75%, #1a8a87 100%);
	background-size: 400% 400%;
	animation: gradientShift 15s ease infinite;
}

@keyframes gradientShift {
	0% { background-position: 0% 50%; }
	50% { background-position: 100% 50%; }
	100% { background-position: 0% 50%; }
}

/* Animated background overlay */
body::before {
	content: '';
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background:
		radial-gradient(ellipse at 20% 20%, rgba(45, 212, 191, 0.15) 0%, transparent 50%),
		radial-gradient(ellipse at 80% 80%, rgba(17, 105, 103, 0.2) 0%, transparent 50%),
		radial-gradient(ellipse at 50% 50%, rgba(94, 234, 212, 0.1) 0%, transparent 70%);
	pointer-events: none;
	z-index: 0;
}

.guest-container {
	height: 100vh;
	display: flex;
	flex-direction: column;
	overflow: hidden;
	position: relative;
	z-index: 1;
}



/* Section 1: Competition Title */
.competition-header {
	background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	padding: 1.25rem 2rem;
	text-align: center;
	box-shadow:
		0 4px 30px rgba(0, 0, 0, 0.1),
		0 1px 0 rgba(255, 255, 255, 0.5) inset;
	flex-shrink: 0;
	border-bottom: 1px solid rgba(17, 105, 103, 0.1);
	position: relative;
	overflow: hidden;
}

.competition-header::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 4px;
	background: linear-gradient(90deg, #116967, #2dd4bf, #5eead4, #2dd4bf, #116967);
	background-size: 200% 100%;
	animation: shimmer 3s linear infinite;
}

@keyframes shimmer {
	0% { background-position: -200% 0; }
	100% { background-position: 200% 0; }
}

.competition-header h1 {
	font-size: 2.2rem;
	font-weight: 800;
	background: linear-gradient(135deg, #116967 0%, #0d5452 30%, #2dd4bf 70%, #116967 100%);
	background-size: 200% 200%;
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
	margin: 0;
	animation: textGradient 5s ease infinite;
	letter-spacing: -0.5px;
}

@keyframes textGradient {
	0%, 100% { background-position: 0% 50%; }
	50% { background-position: 100% 50%; }
}

.competition-header h1 i {
	background: linear-gradient(135deg, #116967 0%, #2dd4bf 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
}

.competition-header .subtitle {
	font-size: 0.95rem;
	color: #64748b;
	margin-top: 0.25rem;
	font-weight: 500;
}

/* Section 2: Stats Cards */
.stats-section {
	padding: 1rem 2rem;
	background: transparent;
	flex-shrink: 0;
}

.stat-card {
	background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.85) 100%);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border-radius: 20px;
	padding: 1.25rem;
	text-align: center;
	height: 100%;
	box-shadow: var(--shadow-md);
	transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
	border: 1px solid rgba(255, 255, 255, 0.5);
	position: relative;
	overflow: hidden;
}

.stat-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: linear-gradient(135deg, transparent 0%, rgba(45, 212, 191, 0.05) 100%);
	opacity: 0;
	transition: opacity 0.4s ease;
}

.stat-card:hover {
	transform: translateY(-8px) scale(1.02);
	box-shadow: var(--shadow-lg), var(--shadow-glow);
}

.stat-card:hover::before {
	opacity: 1;
}

.stat-card .icon-wrapper {
	width: 60px;
	height: 60px;
	border-radius: 16px;
	display: flex;
	align-items: center;
	justify-content: center;
	margin: 0 auto 0.75rem;
	font-size: 1.5rem;
	position: relative;
	overflow: hidden;
}

.stat-card .icon-wrapper::after {
	content: '';
	position: absolute;
	top: -50%;
	left: -50%;
	width: 200%;
	height: 200%;
	background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
	transform: rotate(45deg);
	animation: iconShine 3s ease-in-out infinite;
}

@keyframes iconShine {
	0%, 100% { transform: translateX(-100%) rotate(45deg); }
	50% { transform: translateX(100%) rotate(45deg); }
}

.stat-card .stat-value {
	font-size: 2rem;
	font-weight: 700;
	color: #1e293b;
	line-height: 1.2;
}

.stat-card .stat-label {
	font-size: 0.8rem;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	color: #64748b;
	margin-top: 0.25rem;
	font-weight: 600;
}

.stat-card.primary .icon-wrapper {
	background: linear-gradient(135deg, #116967 0%, #0d5452 50%, #094442 100%);
	color: white;
	box-shadow: 0 8px 20px rgba(17, 105, 103, 0.4);
}

.stat-card.success .icon-wrapper {
	background: linear-gradient(135deg, #2dd4bf 0%, #14b8a6 50%, #0d9488 100%);
	color: white;
	box-shadow: 0 8px 20px rgba(45, 212, 191, 0.4);
}

.stat-card.warning .icon-wrapper {
	background: linear-gradient(135deg, #5eead4 0%, #2dd4bf 50%, #14b8a6 100%);
	color: #0d5452;
	box-shadow: 0 8px 20px rgba(94, 234, 212, 0.4);
}

.stat-card.info .icon-wrapper {
	background: linear-gradient(135deg, #0d5452 0%, #094442 50%, #042f2e 100%);
	color: white;
	box-shadow: 0 8px 20px rgba(13, 84, 82, 0.4);
}

/* Counter Badge Styles */
.counter-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
    color: white;
    font-size: 0.85rem;
    font-weight: 700;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    display: flex;
    align-items: center;
    gap: 0.35rem;
    min-width: 45px;
    justify-content: center;
    transition: all 0.3s ease;
}

.counter-badge i {
    font-size: 0.75rem;
}

.counter-badge.increment {
    animation: counterPulse 0.5s ease;
    transform: scale(1.1);
}

@keyframes counterPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); box-shadow: 0 0 20px rgba(16, 185, 129, 0.6); }
    100% { transform: scale(1); }
}

/* Section 3: Main Content */
.main-content {
	flex: 1;
	padding: 1rem 2rem;
	overflow: hidden;
	display: flex;
	flex-direction: column;
}

.content-wrapper {
	height: 100%;
	display: flex;
	gap: 1.5rem;
	overflow: hidden;
}

.content-wrapper.ended-mode {
	display: block;
}

/* Left Column: Participants Table */
.participants-panel {
	background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border-radius: 24px;
	box-shadow: var(--shadow-lg);
	overflow: hidden;
	display: flex;
	flex-direction: column;
	transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
	border: 1px solid rgba(255, 255, 255, 0.5);
}

.participants-panel.ended-mode {
	display: none;
}

.panel-header {
	background: linear-gradient(135deg, #116967 0%, #0d5452 40%, #094442 100%);
	color: white;
	padding: 1rem 1.5rem;
	font-weight: 600;
	display: flex;
	align-items: center;
	gap: 0.75rem;
	position: relative;
	overflow: hidden;
}

.panel-header::before {
	content: '';
	position: absolute;
	top: 0;
	left: -100%;
	width: 100%;
	height: 100%;
	background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
	animation: headerShine 4s ease-in-out infinite;
}

@keyframes headerShine {
	0%, 100% { left: -100%; }
	50% { left: 100%; }
}

.panel-header i {
	font-size: 1.1rem;
	background: linear-gradient(135deg, #5eead4 0%, #2dd4bf 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
}

.panel-header .badge {
	background: linear-gradient(135deg, rgba(45, 212, 191, 0.2) 0%, rgba(94, 234, 212, 0.3) 100%) !important;
	color: #5eead4 !important;
	font-weight: 700;
	padding: 0.5rem 1rem;
	border-radius: 30px;
	border: 1px solid rgba(94, 234, 212, 0.3);
	backdrop-filter: blur(10px);
}

.panel-body {
	flex: 1;
	overflow-y: auto;
	padding: 0;
}

.participants-table {
	margin: 0;
	font-size: 0.9rem;
}

.participants-table thead th {
	background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%);
	position: sticky;
	top: 0;
	font-weight: 700;
	color: #0d5452;
	padding: 1rem 1.25rem;
	border-bottom: 2px solid rgba(17, 105, 103, 0.15);
	white-space: nowrap;
	text-transform: uppercase;
	font-size: 0.75rem;
	letter-spacing: 0.5px;
}

.participants-table tbody td {
	padding: 0.85rem 1.25rem;
	vertical-align: middle;
	border-bottom: 1px solid rgba(17, 105, 103, 0.08);
	transition: all 0.3s ease;
}

.participants-table tbody tr {
	transition: all 0.3s ease;
}

.participants-table tbody tr:hover {
	background: linear-gradient(135deg, rgba(45, 212, 191, 0.08) 0%, rgba(94, 234, 212, 0.05) 100%);
	transform: scale(1.01);
}

.status-badge {
	padding: 0.4rem 0.9rem;
	border-radius: 30px;
	font-size: 0.7rem;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	display: inline-flex;
	align-items: center;
	gap: 0.35rem;
}

.status-badge::before {
	content: '';
	width: 6px;
	height: 6px;
	border-radius: 50%;
	animation: statusPulse 2s ease-in-out infinite;
}

@keyframes statusPulse {
	0%, 100% { opacity: 1; transform: scale(1); }
	50% { opacity: 0.5; transform: scale(0.8); }
}

.status-badge.ready {
	background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
	color: #047857;
	box-shadow: 0 2px 8px rgba(4, 120, 87, 0.2);
}

.status-badge.ready::before {
	background: #10b981;
}

.status-badge.waiting {
	background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
	color: #b45309;
	box-shadow: 0 2px 8px rgba(180, 83, 9, 0.2);
}

.status-badge.waiting::before {
	background: #f59e0b;
}

.status-badge.answered {
	background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%);
	color: #0d5452;
	box-shadow: 0 2px 8px rgba(17, 105, 103, 0.2);
}

.status-badge.answered::before {
	background: #14b8a6;
}

.status-badge.ended {
	background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
	color: #475569;
	box-shadow: 0 2px 8px rgba(71, 85, 105, 0.15);
}

.status-badge.ended::before {
	background: #94a3b8;
	animation: none;
}

/* Right Column: Question Display */
.question-panel {
	background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border-radius: 24px;
	box-shadow: var(--shadow-lg);
	overflow: hidden;
	display: flex;
	flex-direction: column;
	transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
	border: 1px solid rgba(255, 255, 255, 0.5);
}

.question-panel.ended-mode {
	width: 100% !important;
	max-width: 900px;
	margin: 0 auto;
}

.question-header {
	background: linear-gradient(135deg, #0f172a 0%, #1e293b 30%, #0d5452 70%, #094442 100%);
	color: white;
	padding: 1.25rem 1.75rem;
	display: flex;
	justify-content: space-between;
	align-items: center;
	position: relative;
	overflow: hidden;
}

.question-header::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background:
		radial-gradient(ellipse at 20% 50%, rgba(45, 212, 191, 0.15) 0%, transparent 50%),
		radial-gradient(ellipse at 80% 50%, rgba(94, 234, 212, 0.1) 0%, transparent 50%);
	pointer-events: none;
}

.question-header span {
	display: flex;
	align-items: center;
	font-weight: 600;
	font-size: 1.05rem;
	position: relative;
	z-index: 1;
}

.question-header span i {
	background: linear-gradient(135deg, #5eead4 0%, #2dd4bf 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
}

.timer-display {
	font-size: 2.2rem;
	font-weight: 800;
	font-family: 'Inter', monospace;
	background: linear-gradient(135deg, rgba(45, 212, 191, 0.2) 0%, rgba(94, 234, 212, 0.15) 100%);
	padding: 0.6rem 1.5rem;
	border-radius: 16px;
	min-width: 110px;
	text-align: center;
	border: 1px solid rgba(94, 234, 212, 0.3);
	position: relative;
	z-index: 1;
	text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.timer-display.warning {
	background: linear-gradient(135deg, rgba(251, 191, 36, 0.3) 0%, rgba(245, 158, 11, 0.25) 100%);
	border-color: rgba(251, 191, 36, 0.5);
	color: #fbbf24;
	animation: timerPulse 1s infinite;
	text-shadow: 0 0 20px rgba(251, 191, 36, 0.5);
}

.timer-display.danger {
	background: linear-gradient(135deg, rgba(239, 68, 68, 0.3) 0%, rgba(220, 38, 38, 0.25) 100%);
	border-color: rgba(239, 68, 68, 0.5);
	color: #ef4444;
	animation: timerPulse 0.5s infinite;
	text-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
}

@keyframes timerPulse {
	0%, 100% { opacity: 1; transform: scale(1); }
	50% { opacity: 0.8; transform: scale(1.02); }
}

.question-body {
	flex: 1;
	padding: 2rem;
	overflow-y: auto;
	background: linear-gradient(180deg, rgba(240, 253, 250, 0.5) 0%, rgba(255, 255, 255, 0) 100%);
}

.question-number {
	font-size: 0.85rem;
	color: #0d5452;
	text-transform: uppercase;
	letter-spacing: 2px;
	margin-bottom: 0.75rem;
	font-weight: 700;
	display: inline-flex;
	align-items: center;
	gap: 0.5rem;
	background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%);
	padding: 0.5rem 1rem;
	border-radius: 30px;
}

.question-text {
	font-size: 1.5rem;
	font-weight: 700;
	color: #1e293b;
	line-height: 1.6;
	margin-bottom: 2rem;
	letter-spacing: -0.3px;
}

.options-grid {
	display: flex;
	flex-direction: column;
	gap: 1rem;
}

.option-item {
	display: flex;
	align-items: center;
	padding: 1.25rem 1.5rem;
	background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
	border: 2px solid rgba(17, 105, 103, 0.15);
	border-radius: 16px;
	transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
	cursor: default;
	position: relative;
	overflow: hidden;
}

.option-item::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: linear-gradient(135deg, rgba(45, 212, 191, 0.1) 0%, rgba(94, 234, 212, 0.05) 100%);
	opacity: 0;
	transition: opacity 0.4s ease;
}

.option-item:hover {
	border-color: rgba(17, 105, 103, 0.4);
	transform: translateX(8px);
	box-shadow: var(--shadow-md);
}

.option-item:hover::before {
	opacity: 1;
}

.option-item .option-letter {
	width: 48px;
	height: 48px;
	border-radius: 14px;
	background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
	color: #475569;
	display: flex;
	align-items: center;
	justify-content: center;
	font-weight: 800;
	font-size: 1.2rem;
	margin-right: 1.25rem;
	flex-shrink: 0;
	transition: all 0.4s ease;
	position: relative;
	z-index: 1;
}

.option-item:hover .option-letter {
	background: linear-gradient(135deg, #116967 0%, #0d5452 100%);
	color: white;
	box-shadow: 0 4px 15px rgba(17, 105, 103, 0.4);
}

.option-item .option-text {
	font-size: 1.15rem;
	color: #334155;
	flex: 1;
	font-weight: 500;
	position: relative;
	z-index: 1;
}

.option-item.correct {
	background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 50%, #6ee7b7 100%);
	border-color: #10b981;
	animation: correctPulse 0.6s ease;
	box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

@keyframes correctPulse {
	0% { transform: scale(1); }
	50% { transform: scale(1.02); }
	100% { transform: scale(1); }
}

.option-item.correct .option-letter {
	background: linear-gradient(135deg, #10b981 0%, #059669 100%);
	color: white;
	box-shadow: 0 4px 15px rgba(16, 185, 129, 0.5);
}

.option-item.correct .option-text {
	color: #047857;
	font-weight: 700;
}

.option-item.incorrect {
	background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
	border-color: #ef4444;
	opacity: 0.7;
}

.content-wrapper {
    display: flex;
    gap: 1.5rem;
    width: 100%;
    max-width: 100%;
}

.question-panel {
    flex: 1 1 60%;
    min-width: 0; 
    max-width: 100%;
}

.main-content {
    overflow-x: hidden;
}

/* Waiting State */
.waiting-state {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	height: 100%;
	text-align: center;
	padding: 3rem;
}

.waiting-state .waiting-icon {
	font-size: 5rem;
	margin-bottom: 1.5rem;
	background: linear-gradient(135deg, #2dd4bf 0%, #14b8a6 50%, #0d9488 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
	animation: waitingFloat 3s ease-in-out infinite;
}

@keyframes waitingFloat {
	0%, 100% { transform: translateY(0); }
	50% { transform: translateY(-10px); }
}

.waiting-state h3 {
	background: linear-gradient(135deg, #116967 0%, #0d5452 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
	font-size: 1.75rem;
	font-weight: 700;
	margin-bottom: 0.75rem;
}

.waiting-state p {
	color: #64748b;
	font-size: 1.1rem;
	font-weight: 500;
}

/* Scoreboard */
.scoreboard-container {
	display: none;
	height: 100%;
	overflow-y: auto;
}

.scoreboard-container.active {
	display: block;
}

/* Scrollbar Styling */
.panel-body::-webkit-scrollbar,
.question-body::-webkit-scrollbar {
	width: 8px;
}

.panel-body::-webkit-scrollbar-track,
.question-body::-webkit-scrollbar-track {
	background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%);
	border-radius: 4px;
}

.panel-body::-webkit-scrollbar-thumb,
.question-body::-webkit-scrollbar-thumb {
	background: linear-gradient(135deg, #2dd4bf 0%, #14b8a6 100%);
	border-radius: 4px;
	border: 2px solid #f0fdfa;
}

.panel-body::-webkit-scrollbar-thumb:hover,
.question-body::-webkit-scrollbar-thumb:hover {
	background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
}

/* Responsive adjustments */
@media (max-width: 1200px) {
	.competition-header h1 { font-size: 1.8rem; }
	.stat-card .stat-value { font-size: 1.6rem; }
	.question-text { font-size: 1.3rem; }
}

@media (max-width: 768px) {
	.guest-container {
		height: auto;
		min-height: 100vh;
	}
	html, body { overflow: auto; }
	.stats-section { padding: 0.75rem 1rem; }
	.stat-card { margin-bottom: 0.5rem; }
	.main-content { padding: 0.75rem 1rem; }
}

/* Animation for new data */
@keyframes highlight {
	0% { background: linear-gradient(135deg, rgba(45, 212, 191, 0.3) 0%, rgba(94, 234, 212, 0.2) 100%); }
	100% { background: transparent; }
}

.highlight-row {
	animation: highlight 2s ease-out;
}

.stat-card .logo-wrapper {
	width: 60px;
	height: 60px;
	border-radius: 16px;
	display: flex;
	align-items: center;
	justify-content: center;
	margin: 0 auto 0.75rem;
	background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
	overflow: hidden;
	box-shadow: 0 4px 10px rgba(17, 105, 103, 0.1);
	border: 2px solid rgba(17, 105, 103, 0.1);
}

.stat-card .stat-logo {
	max-width: 85%;
	max-height: 85%;
	width: auto;
	height: auto;
	object-fit: contain;
}

/* Additional professional styling */
.badge.bg-light {
	background: linear-gradient(135deg, rgba(45, 212, 191, 0.15) 0%, rgba(94, 234, 212, 0.2) 100%) !important;
	color: #0d5452 !important;
	font-weight: 700;
	border: 1px solid rgba(17, 105, 103, 0.2);
}

.card {
	border: none;
	border-radius: 16px;
	overflow: hidden;
	box-shadow: var(--shadow-md);
}

.card-header {

}

.card-header h5 {
	color: #116967;
	margin: 0;
	font-weight: 600;
	display: flex;
	align-items: center;
	gap: 0.5rem;
}

.card-header h5 i {
	background: linear-gradient(135deg, #5eead4 0%, #2dd4bf 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
}

.card-body {
	background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}

.alert-warning {
	background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
	border: 1px solid rgba(245, 158, 11, 0.3);
	border-radius: 12px;
	color: #92400e;
	font-weight: 500;
}

.table-striped tbody tr:nth-of-type(odd) {
	background: linear-gradient(135deg, rgba(240, 253, 250, 0.5) 0%, rgba(230, 255, 250, 0.3) 100%);
}

.badge.bg-success {
	background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
	box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.badge.bg-info {
	background: linear-gradient(135deg, #2dd4bf 0%, #14b8a6 100%) !important;
	box-shadow: 0 2px 8px rgba(45, 212, 191, 0.3);
}

.badge.bg-warning {
	background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
	color: #78350f !important;
	box-shadow: 0 2px 8px rgba(251, 191, 36, 0.3);
}

/* Floating particles effect */
.guest-container::before {
	content: '';
	position: fixed;
	width: 300px;
	height: 300px;
	background: radial-gradient(circle, rgba(45, 212, 191, 0.1) 0%, transparent 70%);
	border-radius: 50%;
	top: 10%;
	left: 5%;
	animation: float1 20s ease-in-out infinite;
	pointer-events: none;
	z-index: 0;
}

.guest-container::after {
	content: '';
	position: fixed;
	width: 400px;
	height: 400px;
	background: radial-gradient(circle, rgba(94, 234, 212, 0.08) 0%, transparent 70%);
	border-radius: 50%;
	bottom: 10%;
	right: 5%;
	animation: float2 25s ease-in-out infinite;
	pointer-events: none;
	z-index: 0;
}

@keyframes float1 {
	0%, 100% { transform: translate(0, 0) scale(1); }
	25% { transform: translate(50px, 30px) scale(1.1); }
	50% { transform: translate(20px, 60px) scale(0.9); }
	75% { transform: translate(-30px, 20px) scale(1.05); }
}

@keyframes float2 {
	0%, 100% { transform: translate(0, 0) scale(1); }
	33% { transform: translate(-40px, -30px) scale(1.15); }
	66% { transform: translate(30px, -50px) scale(0.95); }
}

#participantsTable,
#participantsTable * {
    animation: none !important;
    transition: none !important;
}

</style>

<!-- Pusher configuration -->
<meta name="pusher-key" content="17ec3014a90b3757e007">
<meta name="pusher-cluster" content="mt1">
</head>

<body>
<div class="guest-container">
<!-- Part 1: Competition Title -->
<header class="competition-header">
	<h1><i class="fas fa-graduation-cap"></i> The Fifth Annual Academic Debate on English Studies </h1>
</header>

<!-- Part 2: Three Stats Cards -->
<section class="stats-section">
    <div class="row g-3">

        <div class="col-md-4 col-sm-6">
            <div class="stat-card primary" id="card-alzahraa">
                <div class="logo-wrapper">
                    <img src="{{ asset('images/4.jpg') }}" alt="Alzahraa University" class="stat-logo">
                </div>
                <div class="counter-badge" id="counter-alzahraa">
                    <i class="fas fa-check"></i>
                    <span class="counter-value">0</span>
                </div>
				<h3>Alzahraa Univ.</h3>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="stat-card warning" id="card-kufa">
                <div class="logo-wrapper">
                    <img src="{{ asset('images/2.jpg') }}" alt="University of Kufa" class="stat-logo">
                </div>
                <span id="current-question" style="display: none;">-</span>
                <div class="counter-badge" id="counter-kufa">
                    <i class="fas fa-check"></i>
                    <span class="counter-value">0</span>
                </div>
				<h3>University of Kufa</h3>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="stat-card info" id="card-baghdad">
                <div class="logo-wrapper">
                    <img src="{{ asset('images/1.jpg') }}" alt="University of Baghdad" class="stat-logo">
                </div>
                <span id="time-remaining" style="display: none;">--</span>
                <div class="counter-badge" id="counter-baghdad">
                    <i class="fas fa-check"></i>
                    <span class="counter-value">0</span>
                </div>
				<h3>Univ. of Baghdad</h3>
            </div>
        </div>

    </div>
</section>


<!-- Part 3: Main Content (Split View) -->
<main class="main-content">
<div class="content-wrapper" id="content-wrapper">
<!-- Left: Participants Status Table -->
<div class="participants-panel" id="participants-panel" style="flex: 0 0 40%;">
<div class="panel-header">
	<i class="fas fa-users"></i>
	<span>Participants Status</span>
	<span class="badge bg-light text-dark ms-auto" id="participant-count-badge">0</span>
</div>
<!-- Participants Status -->
@if ($currentTest && ($currentTest->isActive() || $currentTest->isWaiting()))
<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<h5><i class="fas fa-users"></i>
@if ($currentTest->isWaiting())
Ready Participants ({{ $stats['ready_participants'] }})
@else
Participants Status
@endif
</h5>
</div>
<div class="card-body">
@if ($stats['ready_participants'] == 0)
<div class="alert alert-warning">
	<i class="fas fa-exclamation-triangle"></i>
	No participants are ready yet. Wait for students to click "I'm Ready".
</div>
@else
<div class="table-responsive">
<table class="table table-striped">
<thead>
<tr>
    <th>Name</th>
    <th>University</th>
    <th>Status</th>
	<th id="answer-header" style="display: none;">Answer</th>
</tr>
</thead>

<tbody id="participantsTable">
@php
$readyParticipants = $currentTest->getReadyParticipants();
@endphp

@foreach ($users as $user)
@if (in_array($user->id, $readyParticipants))
<tr>
    <td>{{ $user->name }}</td>
    <td>{{ $user->university ?? 'N/A' }}</td>
    <td>
        @php
        $hasAnswered = \App\Models\Answer::where(
            'test_id', $currentTest->id
        )
        ->where('user_id', $user->id)
        ->where(
            'question_id',
            $currentTest->current_question_id ?? 0
        )
        ->exists();
        @endphp

        @if ($hasAnswered)
            <span class="badge bg-success">Answered</span>
        @else
            @if ($currentTest->isWaiting())
                <span class="badge bg-info">Ready</span>
            @else
                <span class="badge bg-warning">Waiting</span>
            @endif
        @endif
    </td>
<td class="answer-cell" style="display: none;">
					<span class="${answerClass}">${answerDisplay}</span>
				</td>
</tr>
@endif
@endforeach
</tbody>
</table>

</div>
@endif
</div>
</div>
</div>
</div>
@endif
</div>

<!-- Right: Question Display -->
<div class="question-panel" id="question-panel" >
<div class="question-header">
		<div class="question-number">Question <span id="question-number">1</span></div>
	<div class="timer-display" id="timer-display">--</div>
</div>
<div class="question-body" id="question-body">
	<!-- Waiting State -->
	<div class="waiting-state" id="waiting-state">
		<div class="waiting-icon">
			<i class="fas fa-hourglass-half"></i>
		</div>
		<h3>Waiting for Next Question</h3>
		<p>The exam manager will start the question soon...</p>
	</div>

	<!-- Question Content (Hidden by default) -->
	<div id="question-content" style="display: none;">
		<div class="question-text" id="question-text">Loading question...</div>
		<div class="options-grid" id="options-grid">
			<div class="option-item" data-option="A">
				<span class="option-letter">A</span>
				<span class="option-text" id="option-a">...</span>
			</div>
			<div class="option-item" data-option="B">
				<span class="option-letter">B</span>
				<span class="option-text" id="option-b">...</span>
			</div>
			<div class="option-item" data-option="C">
				<span class="option-letter">C</span>
				<span class="option-text" id="option-c">...</span>
			</div>
			<div class="option-item" data-option="D">
				<span class="option-letter">D</span>
				<span class="option-text" id="option-d">...</span>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@7.2.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

<script>
// Application State
const state = {
	currentTest: null,
	currentQuestion: null,
	questionStartTime: null,
	timeLimit: 35,
	timeRemaining: 0,
	timerInterval: null,
	isEnded: false,
	participants: @json($participantsData ?? []),
	correctAnswer: null,
	hasTimeExpired: false,
	answersRevealed: false,
	// Counter state for correct answers by university
	universityCounters: {
		'Al-Zahraa University for Women': 0,
		'Alzahraa University': 0,
		'Alzahraa Univ.': 0,
		'Al-Zahraa University for Women ': 0,
		'University of Kufa': 0,
		'University of Baghdad': 0,
		'Univ. of Baghdad': 0,
		'University of Babylon': 0
	},
	// Track which questions have been processed for scoring
	processedQuestions: new Set()
};

// LocalStorage key for question persistence
const QUESTION_STATE_KEY = 'guest_question_state';

// Save question state to localStorage
function saveQuestionState() {
	if (state.currentQuestion && !state.isEnded) {
		const questionState = {
			question: state.currentQuestion,
			questionStartTime: state.questionStartTime,
			timeLimit: state.timeLimit,
			timeRemaining: state.timeRemaining,
			correctAnswer: state.correctAnswer,
			hasTimeExpired: state.hasTimeExpired,
			answersRevealed: state.answersRevealed,
			universityCounters: state.universityCounters,
			processedQuestions: Array.from(state.processedQuestions),
			savedAt: Date.now()
		};
		localStorage.setItem(QUESTION_STATE_KEY, JSON.stringify(questionState));
		console.log('Question state saved to localStorage:', questionState);
	}
}

// Restore question state from localStorage
function restoreQuestionState() {
	const savedState = localStorage.getItem(QUESTION_STATE_KEY);
	if (savedState) {
		try {
			const questionState = JSON.parse(savedState);
			console.log('Restoring question state from localStorage:', questionState);

			// Check if the saved state is still valid (not too old)
			const timeSinceSave = Date.now() - (questionState.savedAt || 0);
			const maxAge = 5 * 60 * 1000; // 5 minutes max

			if (timeSinceSave < maxAge && questionState.question) {
				// Restore the state
				state.currentQuestion = questionState.question;
				state.questionStartTime = questionState.questionStartTime;
				state.timeLimit = questionState.timeLimit || 35;
				state.correctAnswer = questionState.correctAnswer;
				state.hasTimeExpired = questionState.hasTimeExpired || false;
				state.answersRevealed = questionState.answersRevealed || false;

				// Restore university counters
				if (questionState.universityCounters) {
					state.universityCounters = questionState.universityCounters;
					updateAllCounters();
				}

				// Restore processed questions
				if (questionState.processedQuestions) {
					state.processedQuestions = new Set(questionState.processedQuestions);
				}

				// Calculate remaining time based on elapsed time since save
				const elapsedSinceSave = Math.floor((Date.now() - questionState.savedAt) / 1000);
				state.timeRemaining = Math.max(0, (questionState.timeRemaining || questionState.timeLimit) - elapsedSinceSave);

				// Update UI to show the question
				updateQuestionDisplay(state.currentQuestion);

				// Start timer with restored remaining time
				restoreTimer();

				// If time had already expired when saved, show correct answer
				if (state.hasTimeExpired && state.correctAnswer) {
					setTimeout(() => {
						highlightCorrectAnswer(state.correctAnswer);
					}, 100);
				}

				console.log('Question state restored successfully');
			} else {
				console.log('Saved question state is too old or invalid, clearing...');
				clearQuestionState();
			}
		} catch (e) {
			console.error('Error restoring question state:', e);
			clearQuestionState();
		}
	}
}

// Clear question state from localStorage
function clearQuestionState() {
	localStorage.removeItem(QUESTION_STATE_KEY);
	console.log('Question state cleared from localStorage');
}

// Update question display without starting timer (used when restoring)
function updateQuestionDisplay(question) {
	if (!question) return;

	// Show question content
	elements.waitingState.style.display = 'none';
	elements.questionContent.style.display = 'block';

	// Update question display
	elements.questionNumber.textContent = question.question_number || '1';
	elements.questionText.textContent = question.title;
	elements.currentQuestion.textContent = '#' + (question.question_number || '1');

	// Update options
	document.getElementById('option-a').textContent = question.option_a;
	document.getElementById('option-b').textContent = question.option_b;
	document.getElementById('option-c').textContent = question.option_c;
	document.getElementById('option-d').textContent = question.option_d;
}

// Restore timer with remaining time
function restoreTimer() {
	// Update displays with restored remaining time
	updateTimerDisplay();
	elements.timeRemaining.textContent = state.timeRemaining + 's';

	// Clear existing timer
	if (state.timerInterval) {
		clearInterval(state.timerInterval);
	}

	// Start countdown with restored remaining time
	state.timerInterval = setInterval(() => {
		state.timeRemaining--;
		updateTimerDisplay();
		elements.timeRemaining.textContent = state.timeRemaining + 's';

		// Save state periodically
		saveQuestionState();

		if (state.timeRemaining <= 0) {
			handleTimeUp();
		}
	}, 1000);
}

// Initialize Echo/Pusher
window.Echo = new Echo({
	broadcaster: 'pusher',
	key: document.querySelector('meta[name="pusher-key"]')?.getAttribute('content') || '17ec3014a90b3757e007',
	cluster: document.querySelector('meta[name="pusher-cluster"]')?.getAttribute('content') || 'mt1',
	forceTLS: true,
	enableLogging: true
});

console.log('Echo initialized for guest view');

// DOM Elements
const elements = {
	totalParticipants: document.getElementById('total-participants'),
	readyCount: document.getElementById('ready-count'),
	currentQuestion: document.getElementById('current-question'),
	timeRemaining: document.getElementById('time-remaining'),
	participantCountBadge: document.getElementById('participant-count-badge'),
	participantsTable: document.getElementById('participantsTable'),
	waitingState: document.getElementById('waiting-state'),
	questionContent: document.getElementById('question-content'),
	questionNumber: document.getElementById('question-number'),
	questionText: document.getElementById('question-text'),
	timerDisplay: document.getElementById('timer-display'),
	optionsGrid: document.getElementById('options-grid'),
	scoreboardContainer: document.getElementById('scoreboard-container'),
	participantsPanel: document.getElementById('participants-panel'),
	questionPanel: document.getElementById('question-panel'),
	contentWrapper: document.getElementById('content-wrapper'),
	scoreboardBody: document.getElementById('scoreboard-body')
};

// Initialize application
document.addEventListener('DOMContentLoaded', function() {
	console.log('Guest Landing Page initialized');
	console.log('Initial participants:', state.participants.length);

	// Immediately hide answers column - answers should not be visible until time is up
	hideAnswersColumn();

	// Update table with initial data from Blade template immediately
	if (state.participants.length > 0) {
		updateParticipantsTable(state.participants);
	}

	// Initialize stats from Blade template
	if (elements.participantCountBadge) {
		elements.participantCountBadge.textContent = {{ $stats['ready_participants'] ?? 0 }};
	}

	// Restore question state from localStorage (in case of page refresh)
	restoreQuestionState();

	// Subscribe to Pusher events
	subscribeToChannel();
	// Fetch fresh data from server immediately
	fetchInitialData();

	// Start polling for updates every 2 seconds (triggers TestUpdated event)
	setInterval(fetchInitialData, 2000);
});

// Subscribe to Pusher channel
function subscribeToChannel() {
	const channel = Echo.channel('quiz-participants');

	channel.subscribed(function() {
		console.log('✓ Guest subscribed to quiz-participants channel');
	}).error(function(error) {
		console.error('❌ Channel subscription error:', error);
	});

	// Listen for all events
	channel.listen('*', function(e) {
		console.log('🎯 EVENT RECEIVED:', e.event, e.data);
		handleEvent(e.event, e.data);
	});

	// Specific event handlers
	channel.listen('.test.started', function(e) {
		console.log('✅ Test started:', e);
		handleTestStarted(e);
	});

	channel.listen('.test.updated', function(e) {
		console.log('✅ Test updated:', e);
		handleTestUpdated(e);
	});

	channel.listen('.participant.ready', function(e) {
		console.log('✅ Participant ready:', e);
		handleParticipantReady(e);
	});

	channel.listen('.question.started', function(e) {
		console.log('✅ Question started:', e);
		handleQuestionStarted(e);
	});

	channel.listen('.answer.received', function(e) {
		console.log('✅ Answer received:', e);
		handleAnswerReceived(e);
	});

	channel.listen('.test.ended', function(e) {
		console.log('✅ Test ended:', e);
		handleTestEnded(e);
	});

	// Listen for new test started
	channel.listen('.test.started', function(e) {
		console.log('✅ New test started:', e);
		handleTestStarted(e);
	});
}

// Event handlers
function handleEvent(eventName, data) {
	// Central event handling
}

function handleTestStarted(data) {
	console.log('✅ Test started:', data);

	// Reset ended state
	state.isEnded = false;

	// Stop any running timer
	if (state.timerInterval) {
		clearInterval(state.timerInterval);
		state.timerInterval = null;
	}

	// Reset timer displays
	elements.timerDisplay.textContent = '--';
	elements.timeRemaining.textContent = '--';

	// Reset question state
	state.currentQuestion = null;
	state.questionStartTime = null;
	state.timeRemaining = 0;
	state.hasTimeExpired = false;
	state.answersRevealed = false;
	state.correctAnswer = null;

	// Reset university counters for new test
	state.universityCounters = {
		'Al-Zahraa University for Women': 0,
		'Alzahraa University': 0,
		'Alzahraa Univ.': 0,
		'Al-Zahraa University for Women ': 0,
		'University of Kufa': 0,
		'University of Baghdad': 0,
		'Univ. of Baghdad': 0,
		'University of Babylon': 0
	};
	state.processedQuestions = new Set();
	updateAllCounters();

	// Clear localStorage state
	clearQuestionState();

	// Reset UI: Show split panels
	elements.participantsPanel.style.display = 'flex';
	elements.questionPanel.style.display = 'flex';

	// Clear correct answer highlighting
	clearCorrectAnswerHighlighting();

	// Hide answers column when new test starts
	hideAnswersColumn();

	// Reset to waiting state in question panel
	elements.waitingState.style.display = 'flex';
	if (elements.questionContent) {
		elements.questionContent.style.display = 'none';
	}
	if (elements.optionsGrid) {
		elements.optionsGrid.style.display = 'none';
	}

	// Update stats if available
	if (data.test) {
		state.currentTest = data.test;
		updateStats();
	}
}


function handleTestUpdated(data) {
	console.log('📊 TestUpdated event received:', data);

	// Update participants from event data
	if (data.participants && Array.isArray(data.participants)) {
		state.participants = data.participants;
		updateParticipantsTable(data.participants);
		console.log('✅ Updated ' + data.participants.length + ' participants');
	}

	// Update stats
	if (data.stats) {
		if (elements.participantCountBadge) {
			elements.participantCountBadge.textContent = data.stats.ready_participants || 0;
		}
		if (elements.readyCount) {
			elements.readyCount.textContent = data.stats.ready_participants || 0;
		}
	}
}

function handleParticipantReady(data) {
	console.log('👤 ParticipantReady event:', data);

	if (data.ready_count !== undefined && elements.readyCount) {
		elements.readyCount.textContent = data.ready_count;
	}
	if (data.ready_count !== undefined && elements.participantCountBadge) {
		elements.participantCountBadge.textContent = data.ready_count;
	}

	// Add or update participant in the list
	if (data.user_name) {
		const newParticipant = {
			id: data.user_id,
			name: data.user_name,
			university: data.university || 'N/A',
			status: 'ready',
			has_answered: false,
			selected_answer: null
		};

		// Check if participant already exists
		const existingIndex = state.participants.findIndex(p => p.id === data.user_id);
		if (existingIndex >= 0) {
			// Update existing participant
			state.participants[existingIndex] = newParticipant;
		} else {
			// Add new participant
			state.participants.push(newParticipant);
		}

		// Update the table
		updateParticipantsTable(state.participants);
		console.log('✅ Participant added/updated:', data.user_name);
	}
}

function handleQuestionStarted(data) {
	const question = data.question || data;
	state.currentQuestion = question;
	state.questionStartTime = data.question_start_time || Math.floor(Date.now() / 1000);
	state.timeLimit = data.time_limit || 35;
	state.hasTimeExpired = false;
	state.answersRevealed = false; // Reset answers revealed state for new question
	state.correctAnswer = null;

	// Hide answers column for new question - answers should only be visible after time expires
	hideAnswersColumn();

	console.log('Question started:', question);
	console.log('Correct answer:', question.correct_answer);

	// Fallback: check if correct_answer is in parent data (from TestUpdated event)
	if (!question.correct_answer && data.correct_answer) {
		question.correct_answer = data.correct_answer;
		console.log('Correct answer from parent:', data.correct_answer);
	}

	// Clear previous correct answer highlighting
	clearCorrectAnswerHighlighting();

	// Update question display
	updateQuestionDisplay(question);

	// Start timer
	startTimer();

	// Save question state to localStorage for persistence on refresh
	saveQuestionState();
}

function handleAnswerReceived(data) {
	console.log('✅ AnswerReceived event:', data);

	// Update participant's answer status in table (but don't reveal yet)
	if (data.user_id) {
		const participant = state.participants.find(p => p.id === data.user_id);
		if (participant) {
			participant.has_answered = true;
			participant.selected_answer = data.selected_answer || data.answer;
			participant.status = 'answered';
			
			// Update the table - answer will be hidden until time is up
			updateParticipantsTable(state.participants);
			console.log('✅ Participant answer stored (will reveal when time is up):', data.user_name);
		}
	}
}

function handleTestEnded(data) {
	state.isEnded = true;

	// Stop timer
	if (state.timerInterval) {
		clearInterval(state.timerInterval);
	}

	// Clear timer display
	elements.timerDisplay.textContent = '--';
	elements.timeRemaining.textContent = '--';

	// Clear question state from localStorage
	clearQuestionState();

	// Redirect to existing scoreboard page
	console.log('Test ended, redirecting to scoreboard page...');
	window.location.href = '/scoreboard';
}

// Fetch initial data from server
let consecutiveErrors = 0;
const maxErrorsBeforePause = 3;
let pollingPaused = false;
let retryTimeout = null;
async function fetchInitialData() {
	// Don't poll if we've paused due to errors
	if (pollingPaused) {
		return;
	}

	try {
		// Call polling endpoint which broadcasts TestUpdated event
		const response = await axios.get('/guest/poll');
		consecutiveErrors = 0;
		console.log('Polling successful');

		// If we received data, update the UI
		if (response.data && response.data.participants) {
			state.participants = response.data.participants;
			updateParticipantsTable(response.data.participants);

			// Update stats if available
			if (response.data.stats) {
				if (elements.participantCountBadge) {
					elements.participantCountBadge.textContent = response.data.stats.ready_participants || 0;
				}
			}
		}

		// Check if test has ended and redirect to scoreboard page
		if (response.data.currentTest && response.data.currentTest.is_ended && !state.isEnded) {
			console.log('Test has ended, redirecting to scoreboard page...');

			// Set ended state to prevent multiple redirects
			state.isEnded = true;

			// Redirect to existing scoreboard page
			window.location.href = '/scoreboard';
			return;
		}
	} catch (error) {
		consecutiveErrors++;
		console.error('Polling error (attempt ' + consecutiveErrors + '):', error.message);

		// Show error in console with more details
		if (error.response) {
			console.error('Server responded with:', error.response.status, error.response.data);
		}

		// Pause polling if too many consecutive errors
		if (consecutiveErrors >= maxErrorsBeforePause) {
			pollingPaused = true;
			console.warn('Too many consecutive polling errors, pausing...');

			// Try to recover after 30 seconds
			retryTimeout = setTimeout(() => {
				console.log('Attempting to resume polling...');
				consecutiveErrors = 0;
				pollingPaused = false;
				fetchInitialData();
			}, 30000);
		}
	}
}

// Update participants table from state
function updateFromState() {
	if (state.participants && Array.isArray(state.participants)) {
		updateParticipantsTable(state.participants);

		// Update stats
		const readyCount = state.participants.filter(p => p.status === 'ready' || p.status === 'waiting' || p.status === 'answered').length;
		if (elements.participantCountBadge) {
			elements.participantCountBadge.textContent = readyCount;
		}
	}
}

// Update participants table
function updateParticipantsTable(participants) {
	state.participants = participants;
	elements.participantCountBadge.textContent = participants.length;

	if (!elements.participantsTable) {
		console.warn('Participants table element not found');
		return;
	}

	let html = '';
	participants.forEach(participant => {
		let statusClass = '';
		let statusText = '';

		if (participant.status === 'ready') {
			statusClass = 'ready';
			statusText = 'Ready';
		} else if (participant.has_answered) {
			statusClass = 'answered';
			statusText = 'Answered';
		} else if (participant.status === 'waiting') {
			statusClass = 'waiting';
			statusText = 'Waiting';
		} else if (participant.status === 'ended') {
			statusClass = 'ended';
			statusText = 'Ended';
		}

			// Determine answer display - only show when time is up
		let answerDisplay = '-';
		let answerClass = 'text-muted';
		
		if (state.answersRevealed && participant.selected_answer) {
			answerDisplay = '<strong>' + participant.selected_answer + '</strong>';
			answerClass = '';
		} else if (participant.selected_answer) {
			// Store answer but don't show it yet
			answerDisplay = '<span class="text-muted">Waiting...</span>';
		}
		html += `
			<tr class="highlight-row">
				<td>
					<strong>${escapeHtml(participant.name)}</strong>
				</td>
				<td>${escapeHtml(participant.university || 'N/A')}</td>
				<td>
					<span class="status-badge ${statusClass}">${statusText}</span>
				</td>
				<td class="answer-cell">
					<span class="${answerClass}">${answerDisplay}</span>
				</td>
			</tr>
		`;
	});

	elements.participantsTable.innerHTML = html || '<tr><td colspan="4" class="text-center text-muted py-4">No participants yet. Waiting for participants to join...</td></tr>';
}

// Add or update single participant
function addOrUpdateParticipant(participant) {
	const existingIndex = state.participants.findIndex(p => p.id === participant.id);

	if (existingIndex >= 0) {
		state.participants[existingIndex] = participant;
	} else {
		state.participants.push(participant);
	}

	updateParticipantsTable(state.participants);
}

// Update participant answer
function updateParticipantAnswer(userId, selectedAnswer) {
	const participant = state.participants.find(p => p.id === userId);
	if (participant) {
		participant.has_answered = true;
		participant.selected_answer = selectedAnswer;
		updateParticipantsTable(state.participants);
	}
}

// Timer functions
function startTimer() {
	// Calculate remaining time based on start time
	const startTime = state.questionStartTime;
	const elapsed = Math.floor((Date.now() / 1000) - startTime);
	state.timeRemaining = Math.max(0, state.timeLimit - elapsed);

	// Update displays
	updateTimerDisplay();
	elements.timeRemaining.textContent = state.timeRemaining + 's';

	// Clear existing timer
	if (state.timerInterval) {
		clearInterval(state.timerInterval);
	}

	// Start countdown
	state.timerInterval = setInterval(() => {
		state.timeRemaining--;
		updateTimerDisplay();
		elements.timeRemaining.textContent = state.timeRemaining + 's';

		if (state.timeRemaining <= 0) {
			handleTimeUp();
		}
	}, 1000);
}

function updateTimerDisplay() {
	elements.timerDisplay.textContent = state.timeRemaining + 's';

	// Update timer styling based on remaining time
	elements.timerDisplay.classList.remove('warning', 'danger');
	if (state.timeRemaining <= 10) {
		elements.timerDisplay.classList.add('danger');
	} else if (state.timeRemaining <= 20) {
		elements.timerDisplay.classList.add('warning');
	}
}

function handleTimeUp() {
	clearInterval(state.timerInterval);
	state.hasTimeExpired = true;

	elements.timerDisplay.textContent = '0s';
	elements.timerDisplay.classList.add('danger');

	console.log('=== TIME UP DEBUG ===');
	console.log('Full state.currentQuestion:', state.currentQuestion);
	console.log('Correct answer from state:', state.correctAnswer);
	console.log('Correct answer from currentQuestion:', state.currentQuestion?.correct_answer);
	

	// Show options when time is up
	if (elements.optionsGrid) {
		elements.optionsGrid.style.display = 'flex';
	}
	if (document.getElementById('options-placeholder')) {
		document.getElementById('options-placeholder').style.display = 'none';
	}

	// Reveal participants' answers in the table
	showAnswersColumn();
	console.log('All participant answers revealed');

	// Highlight correct answer
	if (state.currentQuestion && state.currentQuestion.correct_answer) {
		state.correctAnswer = state.currentQuestion.correct_answer;
		console.log('Highlighting correct answer:', state.currentQuestion.correct_answer);
		highlightCorrectAnswer(state.currentQuestion.correct_answer);
		
		// Calculate and update university scores after highlighting correct answer
		calculateUniversityScores();
	} else {
		console.warn('❌ No correct_answer found in question data!');
		console.warn('Available fields:', Object.keys(state.currentQuestion || {}));
	}

	// Show correct answer in timer area
	elements.timerDisplay.innerHTML = '<i class="fas fa-check"></i>';
}

function highlightCorrectAnswer(correctOption) {
	const options = elements.optionsGrid.querySelectorAll('.option-item');
	console.log('Found options:', options.length);

	options.forEach(option => {
		console.log('Option:', option.dataset.option, 'Correct:', correctOption, 'Match:', option.dataset.option === correctOption);
		if (option.dataset.option === correctOption) {
			option.classList.add('correct');
			console.log('Added .correct class to option', correctOption);
		}
	});
}

function clearCorrectAnswerHighlighting() {
	const options = elements.optionsGrid.querySelectorAll('.option-item');
	options.forEach(option => {
		option.classList.remove('correct', 'incorrect');
	});
}

// University Counter Functions
function calculateUniversityScores() {
	const questionId = state.currentQuestion?.id || state.currentQuestion?.question_number;
	
	// Don't process the same question twice
	if (state.processedQuestions.has(questionId)) {
		console.log('Question already processed, skipping score calculation');
		return;
	}
	
	state.processedQuestions.add(questionId);
	
	// Check if we have participants and correct answer
	if (!state.correctAnswer || !state.participants || !Array.isArray(state.participants)) {
		console.log('No participants or correct answer to calculate scores');
		return;
	}
	
	console.log('Calculating university scores for question:', questionId);
	console.log('Correct answer:', state.correctAnswer);
	console.log('Total participants:', state.participants.length);
	
	// Count correct answers by university
	state.participants.forEach(participant => {
		// Only count if participant answered and their answer is correct
		if (participant.has_answered && participant.selected_answer) {
			// Trim university name and handle variations
			let university = (participant.university || 'Unknown').trim();
			const selectedAnswer = participant.selected_answer.toUpperCase();
			const correctAnswer = state.correctAnswer.toUpperCase();
			
			console.log(`Participant: ${participant.name}, University: "${university}", Answer: ${selectedAnswer}, Correct: ${correctAnswer}`);
			
			if (selectedAnswer === correctAnswer) {
				// Increment counter for this university
				if (state.universityCounters[university] !== undefined) {
					state.universityCounters[university]++;
					console.log(`✅ Correct answer from ${university}! Count: ${state.universityCounters[university]}`);
				} else {
					// Try to find matching university in our counter map
					let found = false;
					for (const key in state.universityCounters) {
						const normalizedKey = key.trim();
						if (university === normalizedKey || university.includes(normalizedKey) || normalizedKey.includes(university)) {
							state.universityCounters[key]++;
							state.universityCounters[normalizedKey] = (state.universityCounters[normalizedKey] || 0) + 1;
							console.log(`✅ Correct answer from "${university}" (matched with "${normalizedKey}")! Count: ${state.universityCounters[normalizedKey]}`);
							found = true;
							break;
						}
					}
					if (!found) {
						console.log(`University "${university}" not found in counter map`);
					}
				}
			}
		}
	});
	
	// Update all counter displays
	updateAllCounters();
	
	// Save state
	saveQuestionState();
}

function updateAllCounters() {
	console.log('Updating all counters:', state.universityCounters);
	
	// Update Alzahraa counter - aggregate all Al-Zahraa variations
	const zahraCounter = document.getElementById('counter-alzahraa');
	if (zahraCounter) {
		const valueSpan = zahraCounter.querySelector('.counter-value');
		if (valueSpan) {
			// Sum all Al-Zahraa variations
			const total = (state.universityCounters['Al-Zahraa University for Women'] || 0) + 
			              (state.universityCounters['Al-Zahraa University for Women '] || 0) +
			              (state.universityCounters['Alzahraa University'] || 0) + 
			              (state.universityCounters['Alzahraa Univ.'] || 0);
			valueSpan.textContent = total;
			
			// Add increment animation
			zahraCounter.classList.remove('increment');
			void zahraCounter.offsetWidth; // Trigger reflow
			zahraCounter.classList.add('increment');
		}
	}
	
	// Update Kufa counter
	const kufaCounter = document.getElementById('counter-kufa');
	if (kufaCounter) {
		const valueSpan = kufaCounter.querySelector('.counter-value');
		if (valueSpan) {
			const total = (state.universityCounters['University of Kufa'] || 0);
			valueSpan.textContent = total;
			
			// Add increment animation
			kufaCounter.classList.remove('increment');
			void kufaCounter.offsetWidth; // Trigger reflow
			kufaCounter.classList.add('increment');
		}
	}
	
	// Update Baghdad counter - aggregate all Baghdad variations
	const baghdadCounter = document.getElementById('counter-baghdad');
	if (baghdadCounter) {
		const valueSpan = baghdadCounter.querySelector('.counter-value');
		if (valueSpan) {
			// Sum all Baghdad variations
			const total = (state.universityCounters['University of Baghdad'] || 0) + 
			              (state.universityCounters['Univ. of Baghdad'] || 0) +
			              (state.universityCounters['University of Babylon'] || 0);
			valueSpan.textContent = total;
			
			// Add increment animation
			baghdadCounter.classList.remove('increment');
			void baghdadCounter.offsetWidth; // Trigger reflow
			baghdadCounter.classList.add('increment');
		}
	}
}


// Show/hide answers column in participants table
function showAnswersColumn() {
	state.answersRevealed = true;
	
	// Show answer header
	const answerHeader = document.getElementById('answer-header');
	if (answerHeader) {
		answerHeader.style.display = '';
	}
	
	// Show all answer cells
	const answerCells = document.querySelectorAll('.answer-cell');
	answerCells.forEach(cell => {
		cell.style.display = '';
	});
	
	// Refresh table to show actual answers
	if (state.participants && Array.isArray(state.participants)) {
		updateParticipantsTable(state.participants);
	}
	
	saveQuestionState();
	console.log('Answers column revealed');
}

function hideAnswersColumn() {
	state.answersRevealed = false;
	
	// Hide answer header
	const answerHeader = document.getElementById('answer-header');
	if (answerHeader) {
		answerHeader.style.display = 'none';
	}
	
	// Hide all answer cells
	const answerCells = document.querySelectorAll('.answer-cell');
	answerCells.forEach(cell => {
		cell.style.display = 'none';
		cell.querySelector('span').textContent = '-';
		cell.querySelector('span').className = 'text-muted';
	});
	
	saveQuestionState();
	console.log('Answers column hidden');
}
// Update stats display
function updateStats() {
	if (state.currentTest) {
		elements.currentQuestion.textContent = state.currentTest.current_question_number || '-';
	}
}

// Utility: Escape HTML
function escapeHtml(text) {
	const div = document.createElement('div');
	div.textContent = text || '';
	return div.innerHTML;
}
</script>
</body>

</html>