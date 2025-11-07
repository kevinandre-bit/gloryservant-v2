@extends('layouts.guest2')

@section('title', 'Frequently Asked Questions')
 <style nonce="{{ $cspNonce ?? '' }}">
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
    }
    .sidebar-nav {
      height: auto;
      overflow-y: auto;
      background-color: #343a40;
      padding-top: 60px;
      transition: all 0.3s ease-in-out;
    }
    .sidebar-nav a {
      display: block;
      color: #fff;
      padding: 12px 20px;
      text-decoration: none;
      border-bottom: 1px solid #495057;
    }
    .sidebar-nav a:hover {
      background-color: #495057;
    }
    .sidebar-hidden {
      display: none;
    }
    .faq-section {
      padding-top: 60px;
      padding-bottom: 40px;
    }
    .faq-card {
      margin-bottom: 20px;
    }
    .section-title {
      margin-top: 50px;
      margin-bottom: 20px;
      font-size: 1.8rem;
      color: #343a40;
      border-bottom: 2px solid #dee2e6;
      padding-bottom: 10px;
    }
    .menu-toggle-btn {
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1050;
    }
    @media (min-width: 768px) {
      .menu-toggle-btn {
        display: none;
      }
    }
  </style>
@section('content')
<body>
  <div class="container faq-section">
    <h2 class="text-center text-primary">Glory Servant – FAQ Guide
</h2>
    <div id="faq-content"></div>
  </div>

  <script>
    const faqData = {
  "Welcoming Message": [
    {
      q: "Kouman pou mwen komanse konvèsasyon sou chatbot la?",
      a: "	1.	👋 Bonjou! Byenveni sou GloryServant. Kijan mwen ka ede w jodi a nan sèvis ou ap bay pou Seyè a? <br> 2. 🙏 Benediksyon! Mèsi paske w ap sèvi avèk pasyon. Di m ki sa ou bezwen asistans pou jodi a.<br> 3. Shalom! Nou kontan wè w isit la. Souple, fè m konnen kijan mwen ka sipòte w nan ministè w lan."
    },
  ],
  "Getting Started: Login & Access": [
    {
      q: "Kouman pou mwen konekte nan Glory Servant?",
      a: "Super fasil! Jis ale nan <a href='https://gloryservant.com'>gloryservant.com</a>, antre imel ou ak modpas, epi klike sou \"Login\". Ou pral ateri dirèkteman sou tablodbò ou a."
    },
    {
      q: "Mwen gen pwoblèm pou konekte: kisa mwen ta dwe fè?",
      a: "Pa stress! Tcheke imel ou ak modpas ou. Si sa a toujou pa mache, lidè ekip ou a oswa administratè ka ede w retabli aksè ou a."
    }
  ],
  "Your Dashboard: What’s Inside?": [
    {
      q: "Kisa mwen ka fè nan tablodbò mwen an?",
      a: "Panse a li kòm sant kòmand ou. Soti isit la ou ka: Tcheke nan ak soti, tcheke prezans ou, soumèt oswa li devosyon, gade orè ou, mande konje, voye demann, mete ajou pwofil pèsonèl ou."
    },
    {
      q: "Mwen se yon administratè: ki karakteristik adisyonèl mwen ka benefisye?",
      a: "Ou gen gwo pouvwa! Administratè yo ka jere itilizatè yo, voye alèt, ajiste anviwònman ak jenere rapò."
    }
  ],
  "Clocking In/Out & Attendance": [
    {
      q: "Kouman pou mwen revèy antre oswa soti?",
      a: "Jis klike sou 'Revèy Antre / Soti' nan meni an, Lè sa a, peze bouton an dwa selon tan ou. Sistèm nan kaptire kote ou ak tan pou ou."
    },
    {
      q: "Èske mwen ka wè istwa nòt mwen an?",
      a: "Wi! Ale nan seksyon 'Prezans' la epi w ap wè yon lis konplè sou tan antre ak sòti ou."
    },
    {
      q: "Prezans mwen montre 0 anreta ak 0 depa bonè; ou anfòm?",
      a: "Sa plis pase oke, sa vle di ou rive alè. Gwo travay! 🎉"
    }
  ],
  "Devotions": [
    {
      q: "Èske mwen ka soumèt pwòp devosyonèl mwen an?",
      a: "Absoliman! Ale nan 'Devosyon' epi klike sou swa 'Soumèt yon nouvo devosyon' oswa 'Apre devosyon an'. Ranpli detay yo tankou dat, mesaj ak ekriti, epi klike sou 'Soumèt'."
    },
    {
      q: "Èske mwen ka li tou devosyon lòt moun?",
      a: "Natirèlman. Tout devosyon pibliye yo disponib nan seksyon 'View devosyonèl'."
    }
  ],
  "My Schedule": [
    {
      q: "Ki kote mwen ka jwenn orè mwen an?",
      a: "Klike sou 'Orè' nan meni gòch la. Ou pral wè chanjman k ap vini yo, devwa yo oswa reyinyon yo."
    },
    {
      q: "E jou konje mwen yo?",
      a: "Ou pral wè jou konje ou yo (tankou Dimanch oswa Samdi) byen make sou orè ou."
    }
  ],
  "Time Off Requests": [
    {
      q: "Kouman mwen ka mande konje?",
      a: "Ale nan seksyon 'Fèy', klike sou 'Nouvo demann konje', ranpli detay yo (kalite, dat, rezon) epi klike sou 'Soumèt'."
    },
    {
      q: "Èske mwen ka tcheke estati aplikasyon mwen an?",
      a: "Wi! Seksyon 'Fèy' yo montre sa ki annatant ak sa yo apwouve."
    }
  ],
  "Making Requests": [
    {
      q: "Ki kalite demann mwen ka soumèt atravè sit la?",
      a: "Ou ka rapòte pwoblèm, mande èd, oswa pataje sijesyon. Kalite demann yo enkli: Koreksyon tan, èd òdinatè, Ekipman oswa resous, Enkyetid jeneral, Sijesyon, randevou oswa kesyon ministeryèl."
    },
    {
      q: "Kouman pou mwen soumèt yon demann?",
      a: "Ale nan 'Demann', chwazi kategori ou a, ekri yon mesaj kout epi klike sou 'Soumèt'. Ou pral kapab swiv li soti nan menm kote a."
    }
  ],
  "Managing My Profile": [
    {
      q: "Kouman pou mwen mete ajou enfòmasyon mwen yo (non, imèl, elatriye)?",
      a: "Klike sou non ou nan kwen anwo dwat epi chwazi 'Mete ajou kont'. Chanje sa ou bezwen, epi tape 'Mizajou' pou sove."
    },
    {
      q: "Kouman mwen ka chanje modpas mwen an?",
      a: "Menm kote! Klike sou non ou, epi chwazi 'Chanje modpas'. Antre ansyen ak nouvo modpas ou, konfime yo epi klike 'Mizajou'."
    },
    {
      q: "Kouman pou mwen dekonekte?",
      a: "Klike sou non ou, apresa chwazi 'Dekonekte'. Se sa!"
    }
  ],
  "Changing the Website Language": [
    {
      q: "Èske mwen ka chanje lang nan sit la?",
      a: "Wi, ou kapab! Senpleman klike sou ikòn drapo ki anlè a dwat nan ekran an epi chwazi lang ou pi pito."
    }
  ],
  "Admin-Only Features": [
    {
      q: "Ki jan yo jere itilizatè yo kòm administratè?",
      a: "Ale nan 'Jesyon itilizatè'. Ou ka ajoute nouvo itilizatè, modifye itilizatè aktyèl yo, oswa dezaktive yon moun si sa nesesè."
    },
    {
      q: "Èske mwen ka voye anons oswa alèt?",
      a: "Wi! Ale nan 'Alèt', chwazi ki moun ki dwe resevwa mesaj la (moun, tout depatman, elatriye), ekri mesaj ou a epi klike 'Voye'."
    },
    {
      q: "Ki kote mwen ka jwenn rapò?",
      a: "Klike sou 'Rapò' oswa administratè a 'Dashboard'. Ou ka filtre pa itilizatè, dat, depatman e menm ekspòte nan PDF oswa Excel."
    }
  ],
  "Volunteers Section": [
    {
      q: "Kouman pou mwen mete ajou enfòmasyon volontè mwen yo?",
      a: "Ale nan 'Volontè', klike sou non w oswa sou pwofil ou epi mete ajou enfòmasyon w yo tankou plasman ekip ak disponiblite."
    },
    {
      q: "Èske mwen ka wè nan ki ekip mwen ye?",
      a: "Wi! Ekip ou, wòl, ak orè yo tout vizib anba 'Volontè'."
    }
  ],
  "Tasks & Suggestions": [
    {
      q: "Kouman pou mwen wè lis travay mwen an?",
      a: "Ale nan seksyon 'Travay' sou tablodbò w la. Ou pral wè atik ki fini ak annatant."
    },
    {
      q: "Mwen gen yon gwo lide: ki jan mwen ka pataje li?",
      a: "Gwo! Sèvi ak seksyon 'Demann' epi chwazi 'Nouvo lide/sijesyon'. Nou ta renmen tande li!"
    }
  ],
  "Requesting Content": [
    {
      q: "Mwen bezwen yon tablo. Kouman mwen ka mande?",
      a: "Jis ranpli <a href='https://form.asana.com/?k=8y4roi6vbCbYSPd1l73jWA&d=34642379201116'>Fòm demann grafik</a>. Ou ta dwe resevwa yon repons nan lespas 24 èdtan."
    },
    {
      q: "E si mwen bezwen fè yon videyo?",
      a: "Sèvi ak <a href='https://form.asana.com/?k=YBGvMD6rPApBhJjn2gEj-w&d=34642379201116'>Fòm demann videyo</a>. Yon manm nan ekip kominikasyon an ap kontakte w byento."
    },
    {
      q: "Mwen ta renmen rankontre ak ekip kominikasyon an. Kouman mwen ka òganize sa a?",
      a: "Fasil! Ranpli <a href='https://form.asana.com/?k=zGNvbvatDUqU_0gXwL_UyA&d=34642379201116'>Fòm Demann Reyinyon/Pwojè</a> epi yon moun ap kontakte ou."
    }
  ],
  "Still Need Help?": [
    {
      q: "Ki moun mwen ta dwe kontakte si mwen bezwen èd?",
      a: "Si yon bagay pa mache oswa ou pa sèten sou yon bagay, admin ou oswa lidè ekip ou a se moun ki ale nan. Epitou asire w ke w ap itilize yon navigatè ki sipòte tankou Chrome oswa Firefox pou pi bon eksperyans."
    }
  ]
};

    const container = document.getElementById("faq-content");
    Object.entries(faqData).forEach(([sectionTitle, faqs], index) => {
      const section = document.createElement("div");
      section.innerHTML = `<h3 class='section-title'>${sectionTitle}</h3>`;

      faqs.forEach((item, i) => {
        const card = document.createElement("div");
        card.className = "card faq-card";
        card.innerHTML = `
          <div class="card-header" id="heading${index}-${i}">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed w-100 text-start" data-bs-toggle="collapse" data-bs-target="#collapse${index}-${i}" aria-expanded="false" aria-controls="collapse${index}-${i}">
                ${item.q}
              </button>
            </h5>
          </div>
          <div id="collapse${index}-${i}" class="collapse" aria-labelledby="heading${index}-${i}" data-bs-parent="#faq-content">
            <div class="card-body">
              ${item.a}
            </div>
          </div>
        `;
        section.appendChild(card);
      });

      container.appendChild(section);
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

@endsection
