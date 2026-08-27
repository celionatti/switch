<?php

declare(strict_types=1);

namespace Switch\Diagram\Renderer;

use Switch\Diagram\Exporters\DbmlExporter;
use Switch\Diagram\Exporters\JsonExporter;
use Switch\Diagram\Exporters\MermaidExporter;
use Switch\Diagram\Schema\TableMetadata;

class DiagramRenderer
{
    /**
     * @param array<string, TableMetadata> $tables
     */
    public function __construct(
        private array $tables = [],
        private string $title = 'Database ER Diagram & Schema Explorer'
    ) {
    }

    /**
     * Render the standalone full-page HTML diagram view.
     */
    public function renderStandalone(): string
    {
        $schemaJson = JsonExporter::export($this->tables);
        $mermaid = MermaidExporter::export($this->tables);
        $dbml = DbmlExporter::export($this->tables);

        return $this->getHtmlTemplate($schemaJson, $mermaid, $dbml, isDrawer: false);
    }

    /**
     * Render the floating injection markup and drawer to append before </body>.
     */
    public function renderDrawer(): string
    {
        $schemaJson = JsonExporter::export($this->tables);
        $mermaid = MermaidExporter::export($this->tables);
        $dbml = DbmlExporter::export($this->tables);

        return $this->getHtmlTemplate($schemaJson, $mermaid, $dbml, isDrawer: true);
    }

    private function getHtmlTemplate(string $schemaJson, string $mermaid, string $dbml, bool $isDrawer): string
    {
        $encodedJson = htmlspecialchars($schemaJson, ENT_QUOTES, 'UTF-8');
        $encodedMermaid = htmlspecialchars($mermaid, ENT_QUOTES, 'UTF-8');
        $encodedDbml = htmlspecialchars($dbml, ENT_QUOTES, 'UTF-8');

        $drawerTrigger = $isDrawer ? '
            <div id="switch-diagram-trigger" onclick="SwitchDiagram.toggle()" title="Open Schema ER Diagram (Alt + D)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                    <path d="M10 6.5h4"></path>
                    <path d="M6.5 10v4"></path>
                    <path d="M17.5 10v4"></path>
                </svg>
                <span>ER Diagram</span>
            </div>
        ' : '';

        $overlayClass = $isDrawer ? 'switch-diagram-drawer-mode switch-diagram-hidden' : 'switch-diagram-standalone-mode';

        return <<<HTML
<!-- Switch Diagram Schema Explorer -->
{$drawerTrigger}
<div id="switch-diagram-container" class="{$overlayClass}">
    <div class="sd-header">
        <div class="sd-header-left">
            <div class="sd-logo">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    <path d="M10 6.5h4" stroke-dasharray="2 2"></path>
                    <path d="M6.5 10v4" stroke-dasharray="2 2"></path>
                </svg>
                <span>Switch Diagram</span>
                <span class="sd-badge">Live ER</span>
            </div>
            <div class="sd-search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="sd-search-input" placeholder="Search tables or columns (/) ..." oninput="SwitchDiagram.filterTables(this.value)">
            </div>
        </div>

        <div class="sd-controls">
            <div class="sd-btn-group">
                <button class="sd-btn" onclick="SwitchDiagram.zoomIn()" title="Zoom In (+)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                </button>
                <button class="sd-btn" id="sd-zoom-val" onclick="SwitchDiagram.resetZoom()" title="Reset Zoom">100%</button>
                <button class="sd-btn" onclick="SwitchDiagram.zoomOut()" title="Zoom Out (-)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                </button>
                <button class="sd-btn" onclick="SwitchDiagram.autoLayout()" title="Auto Organize Layout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/></svg>
                    <span>Auto Layout</span>
                </button>
            </div>

            <div class="sd-dropdown">
                <button class="sd-btn sd-btn-primary" onclick="SwitchDiagram.toggleExportModal()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Export</span>
                </button>
            </div>

            <button class="sd-btn sd-btn-close" onclick="SwitchDiagram.close()" title="Close Diagram (Esc)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
    </div>

    <!-- Main Diagram Viewport -->
    <div class="sd-workspace" id="sd-workspace">
        <svg id="sd-svg-canvas" class="sd-svg-canvas">
            <defs>
                <marker id="sd-arrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                    <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#00f2fe"/>
                </marker>
                <marker id="sd-arrow-virtual" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                    <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#a78bfa"/>
                </marker>
            </defs>
            <g id="sd-connections-group"></g>
        </svg>

        <div id="sd-nodes-container" class="sd-nodes-container"></div>
    </div>

    <!-- Table Details Sidebar Drawer -->
    <div id="sd-sidebar" class="sd-sidebar sd-sidebar-hidden">
        <div class="sd-sidebar-header">
            <div id="sd-sidebar-title" class="sd-sidebar-title">Table Details</div>
            <button class="sd-sidebar-close" onclick="SwitchDiagram.closeSidebar()">&times;</button>
        </div>
        <div id="sd-sidebar-content" class="sd-sidebar-content"></div>
    </div>

    <!-- Export Modal -->
    <div id="sd-export-modal" class="sd-modal sd-modal-hidden">
        <div class="sd-modal-dialog">
            <div class="sd-modal-header">
                <h3>Export Schema Diagram</h3>
                <button onclick="SwitchDiagram.toggleExportModal()">&times;</button>
            </div>
            <div class="sd-modal-body">
                <div class="sd-tabs">
                    <button class="sd-tab sd-tab-active" onclick="SwitchDiagram.switchExportTab('mermaid')">Mermaid.js</button>
                    <button class="sd-tab" onclick="SwitchDiagram.switchExportTab('dbml')">DBML</button>
                    <button class="sd-tab" onclick="SwitchDiagram.switchExportTab('json')">JSON</button>
                </div>
                <textarea id="sd-export-content" readonly class="sd-export-textarea"></textarea>
                <div class="sd-modal-actions">
                    <button class="sd-btn sd-btn-primary" onclick="SwitchDiagram.copyExport()">Copy to Clipboard</button>
                    <button class="sd-btn" onclick="SwitchDiagram.downloadExport()">Download File</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Switch Diagram Styles */
#switch-diagram-trigger {
    position: fixed;
    bottom: 18px;
    left: 18px;
    z-index: 999990;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #0f172a;
    color: #38bdf8;
    border: 1px solid #1e293b;
    border-radius: 24px;
    padding: 7px 15px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    transition: all 0.2s ease;
}
#switch-diagram-trigger:hover {
    background: #1e293b;
    color: #7dd3fc;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(56, 189, 248, 0.2);
}

#switch-diagram-container {
    position: fixed;
    inset: 0;
    z-index: 999999;
    background: #090d16;
    color: #f8fafc;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.switch-diagram-hidden { display: none !important; }

.sd-header {
    height: 56px;
    background: #0f172a;
    border-bottom: 1px solid #1e293b;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    user-select: none;
    z-index: 10;
}
.sd-header-left { display: flex; align-items: center; gap: 20px; }
.sd-logo { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 15px; color: #38bdf8; }
.sd-badge {
    background: #0369a1;
    color: #e0f2fe;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 12px;
    text-transform: uppercase;
}
.sd-search-box {
    position: relative;
    display: flex;
    align-items: center;
}
.sd-search-box svg { position: absolute; left: 10px; color: #64748b; }
.sd-search-box input {
    background: #1e293b;
    border: 1px solid #334155;
    color: #f1f5f9;
    border-radius: 8px;
    padding: 6px 12px 6px 32px;
    font-size: 12px;
    outline: none;
    width: 220px;
    transition: width 0.2s, border-color 0.2s;
}
.sd-search-box input:focus { width: 280px; border-color: #38bdf8; }

.sd-controls { display: flex; align-items: center; gap: 10px; }
.sd-btn-group { display: flex; background: #1e293b; border-radius: 8px; border: 1px solid #334155; overflow: hidden; }
.sd-btn {
    background: transparent;
    color: #94a3b8;
    border: none;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s;
}
.sd-btn:hover { background: #334155; color: #f8fafc; }
.sd-btn-primary {
    background: #0284c7;
    color: #ffffff;
    border-radius: 8px;
}
.sd-btn-primary:hover { background: #0369a1; }
.sd-btn-close { color: #ef4444; border-radius: 8px; }
.sd-btn-close:hover { background: rgba(239, 68, 68, 0.15); color: #f87171; }

.sd-workspace {
    flex: 1;
    position: relative;
    overflow: hidden;
    background-color: #090d16;
    background-image: radial-gradient(#1e293b 1px, transparent 1px);
    background-size: 24px 24px;
    cursor: grab;
}
.sd-workspace:active { cursor: grabbing; }

.sd-svg-canvas {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.sd-nodes-container {
    position: absolute;
    top: 0;
    left: 0;
    transform-origin: 0 0;
    z-index: 2;
}

/* Table Card Node */
.sd-node {
    position: absolute;
    width: 280px;
    background: #0f172a;
    border: 1px solid #1e293b;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    cursor: move;
    user-select: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.sd-node:hover {
    border-color: #38bdf8;
    box-shadow: 0 12px 35px rgba(56, 189, 248, 0.15);
}
.sd-node-active {
    border-color: #00f2fe !important;
    box-shadow: 0 0 0 2px rgba(0, 242, 254, 0.3) !important;
}

.sd-node-header {
    background: #1e293b;
    padding: 10px 14px;
    border-top-left-radius: 9px;
    border-top-right-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #334155;
}
.sd-node-name {
    font-weight: 700;
    font-size: 13px;
    color: #f1f5f9;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sd-node-count {
    background: #0f172a;
    color: #94a3b8;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: 600;
}

.sd-node-columns {
    padding: 6px 0;
    max-height: 320px;
    overflow-y: auto;
}
.sd-col-row {
    padding: 5px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    transition: background 0.15s;
}
.sd-col-row:hover { background: #1e293b; }
.sd-col-left { display: flex; align-items: center; gap: 6px; }
.sd-col-name { color: #e2e8f0; font-weight: 500; }
.sd-col-type { color: #64748b; font-size: 10px; }

.sd-tag {
    font-size: 9px;
    font-weight: 700;
    padding: 1px 4px;
    border-radius: 4px;
    text-transform: uppercase;
}
.sd-tag-pk { background: #f59e0b; color: #78350f; }
.sd-tag-fk { background: #06b6d4; color: #164e63; }
.sd-tag-null { background: #334155; color: #94a3b8; }

/* SVG Connection Lines */
.sd-conn-line {
    fill: none;
    stroke: #00f2fe;
    stroke-width: 2px;
    stroke-linecap: round;
    transition: stroke 0.2s, stroke-width 0.2s;
    pointer-events: stroke;
    cursor: pointer;
}
.sd-conn-line:hover {
    stroke: #38bdf8;
    stroke-width: 3.5px;
}
.sd-conn-virtual {
    stroke: #a78bfa;
    stroke-dasharray: 4 4;
}

/* Sidebar */
.sd-sidebar {
    position: absolute;
    right: 0;
    top: 56px;
    bottom: 0;
    width: 360px;
    background: #0f172a;
    border-left: 1px solid #1e293b;
    z-index: 20;
    display: flex;
    flex-direction: column;
    box-shadow: -10px 0 30px rgba(0,0,0,0.5);
    transition: transform 0.25s ease;
}
.sd-sidebar-hidden { transform: translateX(100%); }
.sd-sidebar-header {
    padding: 16px 20px;
    border-bottom: 1px solid #1e293b;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.sd-sidebar-title { font-weight: 700; font-size: 15px; color: #38bdf8; }
.sd-sidebar-close { background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer; }
.sd-sidebar-content { padding: 20px; overflow-y: auto; flex: 1; font-size: 12px; }

/* Export Modal */
.sd-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: center;
}
.sd-modal-hidden { display: none !important; }
.sd-modal-dialog {
    width: 600px;
    max-width: 90vw;
    background: #0f172a;
    border: 1px solid #334155;
    border-radius: 12px;
    overflow: hidden;
}
.sd-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #1e293b;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.sd-modal-header h3 { margin: 0; font-size: 15px; color: #f8fafc; }
.sd-modal-header button { background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer; }
.sd-modal-body { padding: 20px; }
.sd-tabs { display: flex; gap: 8px; margin-bottom: 14px; }
.sd-tab {
    background: #1e293b;
    border: 1px solid #334155;
    color: #94a3b8;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}
.sd-tab-active { background: #0284c7; color: #fff; border-color: #0284c7; }
.sd-export-textarea {
    width: 100%;
    height: 220px;
    background: #090d16;
    border: 1px solid #1e293b;
    color: #38bdf8;
    font-family: ui-monospace, SFMono-Regular, monospace;
    font-size: 11px;
    padding: 12px;
    border-radius: 8px;
    box-sizing: border-box;
    resize: none;
}
.sd-modal-actions { margin-top: 14px; display: flex; justify-content: flex-end; gap: 10px; }
</style>

<script>
window.SwitchDiagramData = {
    schema: JSON.parse(`{$encodedJson}`),
    mermaid: `{$encodedMermaid}`,
    dbml: `{$encodedDbml}`
};

const SwitchDiagram = {
    zoom: 1,
    panX: 40,
    panY: 40,
    isDragging: false,
    dragTarget: null,
    dragStartX: 0,
    dragStartY: 0,
    positions: {},
    currentExportTab: 'mermaid',

    init() {
        const stored = localStorage.getItem('switch_diagram_positions');
        if (stored) {
            try { this.positions = JSON.parse(stored); } catch(e) {}
        }
        this.renderNodes();
        this.setupPanning();
        this.setupKeyboardShortcuts();
        setTimeout(() => this.drawConnections(), 50);
    },

    toggle() {
        const container = document.getElementById('switch-diagram-container');
        if (container.classList.contains('switch-diagram-hidden')) {
            container.classList.remove('switch-diagram-hidden');
            this.init();
        } else {
            container.classList.add('switch-diagram-hidden');
        }
    },

    close() {
        const container = document.getElementById('switch-diagram-container');
        if (container.classList.contains('switch-diagram-drawer-mode')) {
            container.classList.add('switch-diagram-hidden');
        }
    },

    setupKeyboardShortcuts() {
        window.addEventListener('keydown', (e) => {
            if ((e.altKey && e.key.toLowerCase() === 'd') || (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'd')) {
                e.preventDefault();
                this.toggle();
            }
            if (e.key === 'Escape') {
                this.close();
                this.closeSidebar();
                const modal = document.getElementById('sd-export-modal');
                if (modal) modal.classList.add('sd-modal-hidden');
            }
            if (e.key === '/' && !e.target.matches('input, textarea')) {
                const search = document.getElementById('sd-search-input');
                if (search) {
                    e.preventDefault();
                    search.focus();
                }
            }
        });
    },

    renderNodes() {
        const container = document.getElementById('sd-nodes-container');
        if (!container) return;
        container.innerHTML = '';

        const tables = SwitchDiagramData.schema.tables || [];
        let colIndex = 0, rowIndex = 0;
        const colWidth = 340, rowHeight = 360;

        tables.forEach((table, idx) => {
            const tableId = 'sd-node-' + table.name;
            let posX = this.positions[table.name]?.x;
            let posY = this.positions[table.name]?.y;

            if (posX === undefined || posY === undefined) {
                posX = 40 + (colIndex * colWidth);
                posY = 40 + (rowIndex * rowHeight);
                colIndex++;
                if (colIndex > 3) {
                    colIndex = 0;
                    rowIndex++;
                }
                this.positions[table.name] = { x: posX, y: posY };
            }

            const card = document.createElement('div');
            card.id = tableId;
            card.className = 'sd-node';
            card.style.left = posX + 'px';
            card.style.top = posY + 'px';

            let columnsHtml = '';
            (table.columns || []).forEach(col => {
                let badges = '';
                if (col.is_primary_key) badges += '<span class="sd-tag sd-tag-pk">PK</span> ';
                if (col.is_foreign_key) badges += '<span class="sd-tag sd-tag-fk">FK</span> ';
                if (col.nullable) badges += '<span class="sd-tag sd-tag-null">NULL</span>';

                columnsHtml += `
                    <div class="sd-col-row" id="sd-col-\${table.name}-\${col.name}">
                        <div class="sd-col-left">
                            \${badges}
                            <span class="sd-col-name">\${col.name}</span>
                        </div>
                        <span class="sd-col-type">\${col.type}</span>
                    </div>
                `;
            });

            card.innerHTML = `
                <div class="sd-node-header" onclick="SwitchDiagram.inspectTable('\${table.name}')">
                    <div class="sd-node-name">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        \${table.name}
                    </div>
                    <span class="sd-node-count">\${table.row_count} rows</span>
                </div>
                <div class="sd-node-columns">\${columnsHtml}</div>
            `;

            this.makeDraggable(card, table.name);
            container.appendChild(card);
        });

        this.applyTransform();
    },

    makeDraggable(element, tableName) {
        let startX = 0, startY = 0, initialLeft = 0, initialTop = 0;

        element.onmousedown = (e) => {
            if (e.target.tagName === 'INPUT' || e.target.closest('.sd-sidebar')) return;
            e.stopPropagation();
            startX = e.clientX;
            startY = e.clientY;
            initialLeft = parseInt(element.style.left, 10) || 0;
            initialTop = parseInt(element.style.top, 10) || 0;

            const onMouseMove = (moveEvent) => {
                const dx = (moveEvent.clientX - startX) / this.zoom;
                const dy = (moveEvent.clientY - startY) / this.zoom;
                const newX = initialLeft + dx;
                const newY = initialTop + dy;
                element.style.left = newX + 'px';
                element.style.top = newY + 'px';
                this.positions[tableName] = { x: newX, y: newY };
                this.drawConnections();
            };

            const onMouseUp = () => {
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
                localStorage.setItem('switch_diagram_positions', JSON.stringify(this.positions));
            };

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        };
    },

    setupPanning() {
        const workspace = document.getElementById('sd-workspace');
        if (!workspace) return;

        let startX = 0, startY = 0;

        workspace.onmousedown = (e) => {
            if (e.target.closest('.sd-node') || e.target.closest('.sd-sidebar')) return;
            this.isDragging = true;
            startX = e.clientX - this.panX;
            startY = e.clientY - this.panY;

            const onMouseMove = (moveEvent) => {
                if (!this.isDragging) return;
                this.panX = moveEvent.clientX - startX;
                this.panY = moveEvent.clientY - startY;
                this.applyTransform();
                this.drawConnections();
            };

            const onMouseUp = () => {
                this.isDragging = false;
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            };

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        };

        workspace.onwheel = (e) => {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.05 : 0.05;
            this.setZoom(this.zoom + delta);
        };
    },

    setZoom(newZoom) {
        this.zoom = Math.min(Math.max(0.3, newZoom), 2.5);
        document.getElementById('sd-zoom-val').innerText = Math.round(this.zoom * 100) + '%';
        this.applyTransform();
        this.drawConnections();
    },

    zoomIn() { this.setZoom(this.zoom + 0.15); },
    zoomOut() { this.setZoom(this.zoom - 0.15); },
    resetZoom() { this.panX = 40; this.panY = 40; this.setZoom(1); },

    autoLayout() {
        this.positions = {};
        localStorage.removeItem('switch_diagram_positions');
        this.renderNodes();
        this.drawConnections();
    },

    applyTransform() {
        const container = document.getElementById('sd-nodes-container');
        if (container) {
            container.style.transform = `translate(\${this.panX}px, \${this.panY}px) scale(\${this.zoom})`;
        }
    },

    drawConnections() {
        const group = document.getElementById('sd-connections-group');
        if (!group) return;
        group.innerHTML = '';

        const tables = SwitchDiagramData.schema.tables || [];

        tables.forEach(table => {
            (table.relations || []).forEach(rel => {
                const sourceEl = document.getElementById('sd-node-' + rel.source_table);
                const targetEl = document.getElementById('sd-node-' + rel.target_table);

                if (!sourceEl || !targetEl) return;

                const sLeft = (parseInt(sourceEl.style.left, 10) * this.zoom) + this.panX;
                const sTop = (parseInt(sourceEl.style.top, 10) * this.zoom) + this.panY;
                const sWidth = sourceEl.offsetWidth * this.zoom;
                const sHeight = sourceEl.offsetHeight * this.zoom;

                const tLeft = (parseInt(targetEl.style.left, 10) * this.zoom) + this.panX;
                const tTop = (parseInt(targetEl.style.top, 10) * this.zoom) + this.panY;
                const tWidth = targetEl.offsetWidth * this.zoom;
                const tHeight = targetEl.offsetHeight * this.zoom;

                const x1 = sLeft + (sWidth / 2);
                const y1 = sTop + (sHeight / 2);
                const x2 = tLeft + (tWidth / 2);
                const y2 = tTop + (tHeight / 2);

                const dx = Math.abs(x2 - x1) * 0.5;
                const pathData = `M \${x1} \${y1} C \${x1 + dx} \${y1}, \${x2 - dx} \${y2}, \${x2} \${y2}`;

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', pathData);
                path.setAttribute('class', 'sd-conn-line' + (rel.is_orm_virtual ? ' sd-conn-virtual' : ''));
                path.setAttribute('marker-end', rel.is_orm_virtual ? 'url(#sd-arrow-virtual)' : 'url(#sd-arrow)');
                path.setAttribute('data-relation', rel.relation_name || '');

                group.appendChild(path);
            });
        });
    },

    filterTables(query) {
        query = query.toLowerCase().trim();
        const tables = SwitchDiagramData.schema.tables || [];

        tables.forEach(table => {
            const card = document.getElementById('sd-node-' + table.name);
            if (!card) return;

            const matchesTable = table.name.toLowerCase().includes(query);
            const matchesColumn = (table.columns || []).some(col => col.name.toLowerCase().includes(query));

            if (!query || matchesTable || matchesColumn) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        this.drawConnections();
    },

    inspectTable(tableName) {
        const sidebar = document.getElementById('sd-sidebar');
        const content = document.getElementById('sd-sidebar-content');
        const title = document.getElementById('sd-sidebar-title');

        const table = (SwitchDiagramData.schema.tables || []).find(t => t.name === tableName);
        if (!table || !sidebar) return;

        title.innerHTML = `Table: <code>\${table.name}</code>`;

        let sampleRowsHtml = '<p style="color: #64748b;">No records found.</p>';
        if (table.sample_rows && table.sample_rows.length > 0) {
            const headers = Object.keys(table.sample_rows[0]);
            sampleRowsHtml = `
                <table style="width:100%; border-collapse: collapse; font-size: 11px; margin-top: 10px;">
                    <thead><tr style="border-bottom: 1px solid #334155; color: #38bdf8;">
                        \${headers.map(h => `<th style="padding: 4px 8px; text-align: left;">\${h}</th>`).join('')}
                    </tr></thead>
                    <tbody>
                        \${table.sample_rows.map(row => `
                            <tr style="border-bottom: 1px solid #1e293b;">
                                \${headers.map(h => `<td style="padding: 4px 8px; color: #cbd5e1;">\${row[h] !== null ? row[h] : '<span style="color:#64748b;">NULL</span>'}</td>`).join('')}
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }

        content.innerHTML = `
            <div style="margin-bottom: 16px;">
                <div style="color:#94a3b8; font-size:11px;">Model Class:</div>
                <div style="color:#38bdf8; font-weight:600; font-family: monospace;">\${table.model_class || 'None (Raw Table)'}</div>
            </div>
            <div style="display:flex; gap:10px; margin-bottom: 16px;">
                <span class="sd-badge">\${table.row_count} Rows</span>
                \${table.has_soft_deletes ? '<span class="sd-badge" style="background:#10b981;">Soft Deletes</span>' : ''}
            </div>
            <h4 style="color:#f1f5f9; margin: 16px 0 8px;">Recent Records Preview</h4>
            \${sampleRowsHtml}
        `;

        sidebar.classList.remove('sd-sidebar-hidden');
    },

    closeSidebar() {
        const sidebar = document.getElementById('sd-sidebar');
        if (sidebar) sidebar.classList.add('sd-sidebar-hidden');
    },

    toggleExportModal() {
        const modal = document.getElementById('sd-export-modal');
        if (!modal) return;
        if (modal.classList.contains('sd-modal-hidden')) {
            modal.classList.remove('sd-modal-hidden');
            this.switchExportTab(this.currentExportTab);
        } else {
            modal.classList.add('sd-modal-hidden');
        }
    },

    switchExportTab(tab) {
        this.currentExportTab = tab;
        document.querySelectorAll('.sd-tab').forEach(b => b.classList.remove('sd-tab-active'));
        const activeBtn = Array.from(document.querySelectorAll('.sd-tab')).find(b => b.innerText.toLowerCase().includes(tab));
        if (activeBtn) activeBtn.classList.add('sd-tab-active');

        const textarea = document.getElementById('sd-export-content');
        if (tab === 'mermaid') {
            textarea.value = SwitchDiagramData.mermaid;
        } else if (tab === 'dbml') {
            textarea.value = SwitchDiagramData.dbml;
        } else if (tab === 'json') {
            textarea.value = JSON.stringify(SwitchDiagramData.schema, null, 2);
        }
    },

    copyExport() {
        const textarea = document.getElementById('sd-export-content');
        textarea.select();
        navigator.clipboard.writeText(textarea.value);
        alert('Schema copied to clipboard!');
    },

    downloadExport() {
        const content = document.getElementById('sd-export-content').value;
        const ext = this.currentExportTab === 'json' ? 'json' : (this.currentExportTab === 'dbml' ? 'dbml' : 'mmd');
        const blob = new Blob([content], { type: 'text/plain' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `schema-diagram.\${ext}`;
        a.click();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('switch-diagram-container') && !document.getElementById('switch-diagram-container').classList.contains('switch-diagram-drawer-mode')) {
        SwitchDiagram.init();
    }
});
</script>
HTML;
    }
}
