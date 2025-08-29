<p align="center">
  <img src="https://res.cloudinary.com/djix6uusx/image/upload/v1754362230/blog-logo_jrjmgp.png" width="130px"/>
  <h3 align="center">Github Readme Blog Cards</h3>
</p>

<p align="center">
  Turn your blog links into sleek preview cards — perfect for <br/> showcasing your latest posts right inside your GitHub README!
</p>

<p align="center">
<a href="https://www.php.net/"><img alt="Made with PHP" title="Made with PHP" src="https://img.shields.io/badge/-Made%20with%20PHP-4e5b93?style=for-the-badge&logo=php&logoColor=white"/></a>
<a href="https://render.com/"><img alt="Powered by Render" title="Powered by Render" src="https://img.shields.io/badge/-Powered%20by%20Render-000?style=for-the-badge&logo=render&logoColor=white"/></a>
    
</p>

<br/>

## ⚡ Quick setup

1. Copy-paste the HTML code below into your GitHub profile README
2. Replace `<blog_URL>` with the URL and `Blog Title` with the title of your blog.

```md
<a href="<blog_URL>">
  <img src="https://github-readme-blog-cards.onrender.com?url=<blog_URL>" alt="Blog Title"/>
</a>
```

⚠️ **Important :** Use HTML `<img>` tags instead of Markdown image syntax `![]()`. Blog URLs contain _special_ characters (@, :, /) and additional parameters that can break Markdown parsing, causing images to fail loading. HTML syntax ensures _reliable_ rendering across all scenarios.

### Example :point_down:

```md
<a href="https://medium.com/@RitikaAgrawal08/exploring-css-flexbox-getting-started-with-the-basics-1174eea3ad4e">
  <img src="https://github-readme-blog-cards.onrender.com?url=https://medium.com/@RitikaAgrawal08/exploring-css-flexbox-getting-started-with-the-basics-1174eea3ad4e" alt="CSS Flexbox"/>
</a>
```

<img src='https://res.cloudinary.com/djix6uusx/image/upload/v1754739171/blog-card-demo_qckoao.png' width='250' height='300'/>

<br/>

## 🔧 Options

The `url` field is **required**. All other fields are _optional_.
<br/>

| Parameter |                    Details                    |                                       Example                                        |
| :-------: | :-------------------------------------------: | :----------------------------------------------------------------------------------: |
|   `url`   |       the blog URL to display card for        | `https://medium.com/@RitikaAgrawal08/diving-deep-into-z-index-property-d60e3443f4ec` |
| `layout`  |   layout for the card (default: `vertical`)   |                              `vertical` or `horizontal`                              |
|  `theme`  | color theme for the card (default: `default`) |                           `dark`, `pastel`, `carbon` etc.                            |

<br/>

## 🖼 Layouts

To use a **layout**, append `&layout=` followed by layout value available at the end of source URL :

```md
<a href="<blog_URL>">
  <img src="https://github-readme-blog-cards.onrender.com?url=<blog_URL>&layout=horizontal" alt="Blog Title"/>
</a>
```

<br/>

|           Layout           |                                                                 Preview                                                                  |
| :------------------------: | :--------------------------------------------------------------------------------------------------------------------------------------: |
| `vertical` <br/> (default) |   <br/> <img src='https://res.cloudinary.com/djix6uusx/image/upload/v1754739171/blog-card-demo_qckoao.png' width='250' height='300'/>    |
|        `horizontal`        | <br/> <img src='https://res.cloudinary.com/djix6uusx/image/upload/v1754739711/blog-card-horizontal_f1ctcz.jpg' width='385' height='110'> |

<br/>

## 🌈 Themes

To use a **theme**, append `&theme=` followed by a theme value available at the end of source URL :

```md
<a href="<blog_URL>">
  <img src="https://github-readme-blog-cards.onrender.com?url=<blog_URL>&theme=dark" alt="Blog Title"/>
</a>
```

<br/>

|           Theme           |                                                                  Preview                                                                  |
| :-----------------------: | :---------------------------------------------------------------------------------------------------------------------------------------: |
| `default` <br/> (default) |    <br/> <img src='https://res.cloudinary.com/djix6uusx/image/upload/v1754739171/blog-card-demo_qckoao.png' width='250' height='300'/>    |
|          `sunny`          |   <br/> <img src='https://res.cloudinary.com/djix6uusx/image/upload/v1754739940/blog-card-sunny_y8cubp.png' width='250' height='300'/>    |
|       `light-gray`        | <br/> <img src="https://res.cloudinary.com/djix6uusx/image/upload/v1754740352/blog-card-light-gray_pk0bsm.png" width='250' height='300'/> |
|         `pastel`          |  <br/> <img src="https://res.cloudinary.com/djix6uusx/image/upload/v1754740826/blog-card-pastel_izphr2.png" width='250' height='300' />   |
|          `mint`           |   <br/> <img src="https://res.cloudinary.com/djix6uusx/image/upload/v1754740745/blog-card-mint_o1i1ms.png" width='250' height='300' />    |
|          `dark`           |    <br/> <img src="https://res.cloudinary.com/djix6uusx/image/upload/v1754740660/blog-card-dark_ovjasp.png" width='250' height='300'/>    |
|         `carbon`          |  <br/> <img src="https://res.cloudinary.com/djix6uusx/image/upload/v1754741699/blog-card-carbon_hd2jg7.png" width='250' height='300' />   |
|         `dracula`         |  <br/> <img src="https://res.cloudinary.com/djix6uusx/image/upload/v1754741818/blog-card-dracula_jkc2vs.png" width='250' height='300' />  |
|         `copper`          |  <br/> <img src="https://res.cloudinary.com/djix6uusx/image/upload/v1754742314/blog-card-copper_csmue1.png" width='250' height='300' />   |

<br/>

## 🤗 Contributions

Contributions are most welcome! Feel free to [open an issue](https://github.com/Ritika-Agrawal811/github-readme-blog-cards/issues/new/choose) or submit a [pull request](https://github.com/Ritika-Agrawal811/github-readme-blog-cards/compare) if you have a way to improve this project.

Make sure your request is meaningful and you have tested the app locally before submitting a pull request.

Refer to [CONTRIBUTING.md](/CONTRIBUTING.md) for more details on contributing, installing requirements, and running the application.

## 🙋‍♂️ Support

Give a ⭐ if you like this project, and share it with your friends!

<p align="left">
  <a href="https://buymeacoffee.com/ritikaagrawal08"><img alt="Buy me a coffee" title="Buy me a coffee" src="https://img.shields.io/badge/-Buy%20me%20a%20coffee-yellow?style=for-the-badge&logo=buymeacoffee&logoColor=white"/></a>
</p>

<p align='center'>• • •</p>

#### Thanks so much! Happy Coding! ✨

Inspired by [Github Readme Streak Stats](https://github.com/DenverCoder1/github-readme-streak-stats) :fire:
