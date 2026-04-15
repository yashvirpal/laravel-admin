<div id="fake-notification" style="
    position: fixed;
    bottom: 20px;
    left: 20px;
    width: 320px;
    background: #fff;
    color: #333;
    border-radius: 12px;
    display: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    overflow: hidden;
    font-family: Arial;
    z-index: 9999;
    cursor: pointer;
">

    <div style="display:flex; align-items:center; padding:12px;">

        <!-- Avatar (initials) -->
        <div id="user-avatar" style="
            width:40px;
            height:40px;
            border-radius:50%;
            margin-right:10px;
            background:#4f46e5;
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:600;
            font-size:14px;
            text-transform:uppercase;
            flex-shrink:0;
        "></div>

        <div style="flex:1;">
            <div id="notification-text" style="font-size:13px;font-weight:500;"></div>
            <div id="notification-time" style="font-size:11px;color:gray;"></div>
        </div>

        <!-- Product -->
        <img id="product-image" style="width:55px;height:55px;object-fit:cover;border-radius:8px;">
    </div>

</div>

<script>
(function () {

    const products = @json($products ?? []);

    const fallbackProducts = [
        {
            title: 'Customised Bracelets',
            image: 'https://via.placeholder.com/60',
            price: 999,
            slug: '#'
        }
    ];

    const data = products.length ? products : fallbackProducts;

    const users = [
        { name: 'Rahul Sharma', city: 'Delhi' },
        { name: 'Amit Verma', city: 'Noida' },
        { name: 'Sneha Gupta', city: 'Gurgaon' },
        { name: 'Priya Singh', city: 'Mumbai' },
        { name: 'Arjun Mehta', city: 'Bangalore' },
        { name: 'Neha Kapoor', city: 'Pune' },
        { name: 'Karan Patel', city: 'Hyderabad' },
        { name: 'Vikas Yadav', city: 'Lucknow' },
        { name: 'Rohit Jain', city: 'Jaipur' },
        { name: 'Ananya Das', city: 'Kolkata' }
    ];

    let currentLink = '#';
    let secondsAgo = 0;
    let isShowing = false;

    function randomUser() {
        return users[Math.floor(Math.random() * users.length)];
    }

    function randomItem(arr) {
        return arr[Math.floor(Math.random() * arr.length)];
    }

    function getInitials(name) {
        return name
            .split(' ')
            .map(n => n[0])
            .join('')
            .substring(0, 2)
            .toUpperCase();
    }

    function updateTimer() {
        const el = document.getElementById('notification-time');
        if (!el || !isShowing) return;

        if (secondsAgo < 60) {
            el.innerText = `${secondsAgo} sec ago`;
        } else {
            el.innerText = `${Math.floor(secondsAgo / 60)} mins ago`;
        }

        secondsAgo++;
    }

    function showNotification() {

        if (isShowing) return; // prevent overlap

        const user = randomUser();
        const product = randomItem(data);

        const el = document.getElementById('fake-notification');

        isShowing = true;

        document.getElementById('notification-text').innerText =
            `${user.name} from ${user.city} purchased ${product.title} - ₹${product.price}`;

        document.getElementById('user-avatar').innerText = getInitials(user.name);

        document.getElementById('product-image').src = product.image;

        currentLink = product.slug ? `/product/${product.slug}` : '#';
        secondsAgo = 1;

        if (window.innerWidth < 768) {
            el.style.bottom = 'auto';
            el.style.top = '20px';
        }

        el.style.display = 'block';
        el.style.transform = 'translateY(20px)';
        el.style.opacity = 0;

        setTimeout(() => {
            el.style.transition = 'all 0.4s';
            el.style.transform = 'translateY(0)';
            el.style.opacity = 1;
        }, 50);

        // auto hide
        setTimeout(() => {
            el.style.opacity = 0;

            setTimeout(() => {
                el.style.display = 'none';
                isShowing = false; // allow next
            }, 300);

        }, 4500);
    }

    // click redirect
    document.addEventListener('DOMContentLoaded', function () {
        const box = document.getElementById('fake-notification');

        box.addEventListener('click', () => {
            if (currentLink && currentLink !== '#') {
                window.location.href = currentLink;
            }
        });
    });

    // timers
    setInterval(updateTimer, 1000);

    setTimeout(() => {
        showNotification();
        setInterval(showNotification, 7000);
    }, 2000);

})();
</script>