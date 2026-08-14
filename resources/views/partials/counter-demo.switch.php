<div id="live-counter-box" class="counter-card" switch-preserve-scroll>
    <div class="counter-header">
        <span class="counter-tag">⚡ Live Reactive Component</span>
        <span class="counter-badge">No Page Reload</span>
    </div>
    
    <div class="counter-number">{{ $count }}</div>
    <div class="counter-label">Sub-millisecond Server State Sync</div>

    <div class="counter-actions">
        <button 
            type="button"
            class="btn-counter btn-decrement"
            switch-action="/live/counter/decrement" 
            switch-data='{"count": {{ $count }} }'
            switch-target="#live-counter-box"
            switch-disable
        >
            −
        </button>

        <button 
            type="button"
            class="btn-counter btn-increment"
            switch-action="/live/counter/increment" 
            switch-data='{"count": {{ $count }} }'
            switch-target="#live-counter-box"
            switch-disable
        >
            +
        </button>
    </div>
</div>
