<extends name="layouts.app" />

<section name="title">{{ $title }}</section>

<section name="content">
    <h1>Welcome to {{ $appName }}</h1>
    <p>Status: <span class="badge">Active</span></p>

    <if cond="$user">
        <p>Logged in as: <strong>{{ $user.name }}</strong> ({{ $user.role }})</p>
    <else/>
        <p>Guest User</p>
    </if>

    <h3>Database ORM Products</h3>
    <ul>
        <foreach items="$products" as="$product">
            <li><strong>{{ $product.name }}</strong> — ${{ $product.price }}</li>
        </foreach>
    </ul>

    <h3>Framework Subsystems</h3>
    <ul>
        <foreach items="$subsystems" as="$sys">
            <li>{{ $sys }}</li>
        </foreach>
    </ul>
</section>
