<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title> 404 Forbidden</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html,
body{
    width:100%;
    height:100%;
    overflow:hidden;
    background:#36383f;
    font-family:"Montserrat",sans-serif;
    font-size:20px;
}

.error-page{
    width:100%;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

/* ========================================= */

:root{
    --hole-distance:25px;
}

#sign-wrapper{

    position:relative;

    width:min(820px,100%);
    max-width:820px;

    background:#f4f4f4;

    border:1px solid #e9ecf0;
    border-radius:40px;

    padding:45px;

    box-shadow:0 20px 60px rgba(0,0,0,.35);

}

/* ========================================= */

.hole{
    width:20px;
    height:20px;

    position:absolute;

    border-radius:50%;

    background-image:
    radial-gradient(circle at 99%,#f4f4f4 10%,grey 70%);

    transform:rotate(45deg);
}

#hole1{
    top:var(--hole-distance);
    left:var(--hole-distance);
}

#hole2{
    top:var(--hole-distance);
    right:var(--hole-distance);
}

#hole3{
    bottom:var(--hole-distance);
    left:var(--hole-distance);
}

#hole4{
    bottom:var(--hole-distance);
    right:var(--hole-distance);
}

/* ========================================= */

#header{

    background:#ef5350;

    border-radius:24px 24px 0 0;

    text-align:center;

    padding:25px;

    position:relative;
}

h1{

    color:#fff;

    font-size:4.5rem;

    text-transform:uppercase;

    font-weight:900;

    letter-spacing:3px;

    line-height:1;
}

/* ========================================= */

.strike{

    position:absolute;

    width:22%;

    height:8px;

    background:#fff;
}

#strike1{
    left:60px;
    top:105px;
}

#strike2{
    right:60px;
    top:105px;
}

/* ========================================= */

#sign-body{

    display:flex;

    align-items:center;

    justify-content:center;

    gap:35px;

    padding-top:35px;
}

#copy-container{

    flex:1;
}

#circle-container{

    width:250px;

    flex-shrink:0;
}

#circle-container svg{

    width:100%;
    height:auto;
}

h2{

    font-size:2.2rem;

    text-transform:uppercase;

    text-align:center;

    color:#1d1e22;

    margin-bottom:20px;

    line-height:1.1;
}

p{

    text-align:center;

    color:#4b5563;

    font-size:1rem;

    line-height:1.7;
}

strong{

    color:#ef5350;
}

/* ========================================= */

@media(max-width:900px){

    html{
        font-size:18px;
    }

    #sign-body{

        flex-direction:column;

        text-align:center;
    }

    #circle-container{

        width:200px;
    }

}

@media(max-width:600px){

    html{
        font-size:16px;
    }

    #sign-wrapper{

        border-radius:24px;

        padding:25px;
    }

    #header{

        border-radius:18px 18px 0 0;
    }

    h1{

        font-size:3rem;
    }

    h2{

        font-size:1.6rem;
    }

    .strike{

        display:none;
    }

    .hole{

        width:12px;
        height:12px;
    }

    :root{

        --hole-distance:10px;
    }

}
</style>

</head>

<body>

<div class="error-page">

    <div id="sign-wrapper">

        <div id="hole1" class="hole"></div>
        <div id="hole2" class="hole"></div>
        <div id="hole3" class="hole"></div>
        <div id="hole4" class="hole"></div>

        <header id="header">

            <h1>404 Forbidden</h1>

            <div id="strike1" class="strike"></div>
            <div id="strike2" class="strike"></div>

        </header>

        <section id="sign-body">

            <div id="copy-container">

                <h2>We cannot find the url you want.</h2>

                <p>
                    <strong>Error 404: Forbidden.</strong><br>
                    You do not have permission to access this page.
                </p>

            </div>

            <div id="circle-container">

                <svg viewBox="0 0 500 500">

                    <defs>

                        <pattern
                            id="image"
                            patternUnits="userSpaceOnUse"
                            width="450"
                            height="450">

                        </pattern>

                    </defs>

                    <circle
                        cx="250"
                        cy="250"
                        r="200"
                        stroke="#ef5350"
                        stroke-width="40"
                        fill="url(#image)"/>

                    <line
                        x1="100"
                        y1="100"
                        x2="400"
                        y2="400"
                        stroke="#ef5350"
                        stroke-width="40"/>

                </svg>

            </div>

        </section>

    </div>

</div>

</body>
</html>