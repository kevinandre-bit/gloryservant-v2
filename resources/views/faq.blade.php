@extends('layouts.guest')

@section('title', 'Frequently Asked Questions')

@section('content')

<!--<section class="bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card card-style1 border-0">
                            <div class="card-body p-4 p-md-5 p-xl-6">
                                <div class="text-center mb-2-3 mb-lg-6">
                                    <span class="section-title text-primary">FAQ's</span>
                                    <h2 class="h1 mb-0 text-secondary">Frequently Asked Questions</h2>
                                </div>
                                <div id="accordion" class="accordion-style">
                                    <div class="card mb-3">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"><span class="text-theme-secondary me-2">Q.</span> Can I book online?</button>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-bs-parent="#accordion">
                                            <div class="card-body">
                                                It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-3">
                                        <div class="card-header" id="headingTwo">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"><span class="text-theme-secondary me-2">Q.</span> How would I plan a golf trip for my gathering?</button>
                                            </h5>
                                        </div>
                                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-bs-parent="#accordion">
                                            <div class="card-body">
                                                There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-3">
                                        <div class="card-header" id="headingThree">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree"><span class="text-theme-secondary me-2">Q.</span> What is the dress code?</button>
                                            </h5>
                                        </div>
                                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-bs-parent="#accordion">
                                            <div class="card-body">
                                                Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-3">
                                        <div class="card-header" id="headingFour">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour"><span class="text-theme-secondary me-2">Q.</span> What are the odds of making a double eagle?</button>
                                            </h5>
                                        </div>
                                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-bs-parent="#accordion">
                                            <div class="card-body">
                                                course usually has its golf cart rules on its scorecard or posted in the clubhouse or near the first tee, so be sure to check those out but don’t sweat it! The fact that you are getting some exercise, and hanging out with some good friends!
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="headingFive">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive"><span class="text-theme-secondary me-2">Q.</span> If I need to drop my round would i be able to get a discount?</button>
                                            </h5>
                                        </div>
                                        <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-bs-parent="#accordion">
                                            <div class="card-body">
                                                It was popularised in the with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>--> 
        <!-- FAQ 3 - Bootstrap Brain Component -->
<section class="bsb-faq-3 py-3 py-md-5 py-xl-8">
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6">
        <h2 class="mb-4 display-5 text-center">Frequently Asked Questions</h2>
        <p class="text-secondary text-center lead fs-4">Welcome to the official FAQ page of the Communication Ministry of Tabernacle of Glory.</p>
        <p class="mb-5 text-center">This resource is designed to help ministries, ministries, and members of our church community connect with us more effectively. Whether you’re seeking to request media support, inquire about communication procedures, or simply find the right contact information, you’ll find clear and concise answers here.</p>
        <hr class="w-50 mx-auto mb-5 mb-xl-9 border-dark-subtle">
      </div>
    </div>
  </div>

  <!-- FAQs: Communication & Event Support -->
<div class="mb-8">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-11 col-xl-10">
        <div class="d-flex align-items-end mb-5">
          <i class="bi bi-megaphone me-3 lh-1 display-5"></i>
          <h3 class="m-0">40 Days 2025</h3>
        </div>
      </div>
      <div class="col-11 col-xl-10">
        <div class="accordion accordion-flush" id="faqCommunication">
          
          <!-- Content Availability -->
          <div class="accordion-item bg-transparent border-top border-bottom py-3">
            <h2 class="accordion-header" id="faqCommunicationHeading1">
              <button class="accordion-button collapsed bg-transparent fw-bold shadow-none link-primary" type="button" data-bs-toggle="collapse" data-bs-target="#faqCommunicationCollapse1" aria-expanded="false" aria-controls="faqCommunicationCollapse1">
                Content Availability
              </button>
            </h2>
            <div id="faqCommunicationCollapse1" class="accordion-collapse collapse" aria-labelledby="faqCommunicationHeading1">
              <div class="accordion-body">
                <ul>
                  <li>Is the promo graphic/video for [specific event] ready for my campus?</li>
                  <li>Where can I access downloadable assets (banners, flyers, countdowns)?</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Requesting Content -->
          <div class="accordion-item bg-transparent border-bottom py-3">
            <h2 class="accordion-header" id="faqCommunicationHeading2">
              <button class="accordion-button collapsed bg-transparent fw-bold shadow-none link-primary" type="button" data-bs-toggle="collapse" data-bs-target="#faqCommunicationCollapse2" aria-expanded="false" aria-controls="faqCommunicationCollapse2">
                Requesting Content
              </button>
            </h2>
            <div id="faqCommunicationCollapse2" class="accordion-collapse collapse" aria-labelledby="faqCommunicationHeading2">
              <div class="accordion-body">
                <ul>
                  <li>How do I request custom graphics or video content?</li>
                  <li>What’s the turnaround time for content requests?</li>
                  <li>Can I request edits to a delivered graphic/video?</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Radio & Broadcast Information -->
          <div class="accordion-item bg-transparent border-bottom py-3">
            <h2 class="accordion-header" id="faqCommunicationHeading3">
              <button class="accordion-button collapsed bg-transparent fw-bold shadow-none link-primary" type="button" data-bs-toggle="collapse" data-bs-target="#faqCommunicationCollapse3" aria-expanded="false" aria-controls="faqCommunicationCollapse3">
                Radio & Broadcast Information
              </button>
            </h2>
            <div id="faqCommunicationCollapse3" class="accordion-collapse collapse" aria-labelledby="faqCommunicationHeading3">
              <div class="accordion-body">
                <ul>
                  <li>What is the radio frequency for my region/campus?</li>
                  <li>How can I listen to the live broadcast online?</li>
                  <li>What platforms are we using to broadcast the upcoming event?</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Project Collaboration -->
          <div class="accordion-item bg-transparent border-bottom py-3">
            <h2 class="accordion-header" id="faqCommunicationHeading4">
              <button class="accordion-button collapsed bg-transparent fw-bold shadow-none link-primary" type="button" data-bs-toggle="collapse" data-bs-target="#faqCommunicationCollapse4" aria-expanded="false" aria-controls="faqCommunicationCollapse4">
                Project Collaboration
              </button>
            </h2>
            <div id="faqCommunicationCollapse4" class="accordion-collapse collapse" aria-labelledby="faqCommunicationHeading4">
              <div class="accordion-body">
                <ul>
                  <li>How do I propose a communication project or idea?</li>
                  <li>Who do I contact for video coverage or event livestream?</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Campus Communication Protocols -->
          <div class="accordion-item bg-transparent border-bottom py-3">
            <h2 class="accordion-header" id="faqCommunicationHeading5">
              <button class="accordion-button collapsed bg-transparent fw-bold shadow-none link-primary" type="button" data-bs-toggle="collapse" data-bs-target="#faqCommunicationCollapse5" aria-expanded="false" aria-controls="faqCommunicationCollapse5">
                Campus Communication Protocols
              </button>
            </h2>
            <div id="faqCommunicationCollapse5" class="accordion-collapse collapse" aria-labelledby="faqCommunicationHeading5">
              <div class="accordion-body">
                <ul>
                  <li>What’s the proper channel to communicate with the main Communication Ministry?</li>
                  <li>Can I use TG logos or branding for local initiatives?</li>
                  <li>What are the guidelines for social media posts from a campus?</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Event Promotion -->
          <div class="accordion-item bg-transparent border-bottom py-3">
            <h2 class="accordion-header" id="faqCommunicationHeading6">
              <button class="accordion-button collapsed bg-transparent fw-bold shadow-none link-primary" type="button" data-bs-toggle="collapse" data-bs-target="#faqCommunicationCollapse6" aria-expanded="false" aria-controls="faqCommunicationCollapse6">
                Event Promotion
              </button>
            </h2>
            <div id="faqCommunicationCollapse6" class="accordion-collapse collapse" aria-labelledby="faqCommunicationHeading6">
              <div class="accordion-body">
                <ul>
                  <li>How do I promote my campus event through TG’s main platforms?</li>
                  <li>When should I submit my event materials to be promoted?</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Ministry Tools & Resources -->
          <div class="accordion-item bg-transparent border-bottom py-3">
            <h2 class="accordion-header" id="faqCommunicationHeading7">
              <button class="accordion-button collapsed bg-transparent fw-bold shadow-none link-primary" type="button" data-bs-toggle="collapse" data-bs-target="#faqCommunicationCollapse7" aria-expanded="false" aria-controls="faqCommunicationCollapse7">
                Ministry Tools & Resources
              </button>
            </h2>
            <div id="faqCommunicationCollapse7" class="accordion-collapse collapse" aria-labelledby="faqCommunicationHeading7">
              <div class="accordion-body">
                <ul>
                  <li>What communication templates are available (email, flyer, slide deck)?</li>
                  <li>How can I get access to our media cloud or asset library?</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Technical Support -->
          <div class="accordion-item bg-transparent border-bottom py-3">
            <h2 class="accordion-header" id="faqCommunicationHeading8">
              <button class="accordion-button collapsed bg-transparent fw-bold shadow-none link-primary" type="button" data-bs-toggle="collapse" data-bs-target="#faqCommunicationCollapse8" aria-expanded="false" aria-controls="faqCommunicationCollapse8">
                Technical Support
              </button>
            </h2>
            <div id="faqCommunicationCollapse8" class="accordion-collapse collapse" aria-labelledby="faqCommunicationHeading8">
              <div class="accordion-body">
                <ul>
                  <li>Who do I contact if I’m having trouble downloading a file?</li>
                  <li>What specs should I use for screen displays during events?</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Training & Onboarding -->
          <div class="accordion-item bg-transparent border-bottom py-3">
            <h2 class="accordion-header" id="faqCommunicationHeading9">
              <button class="accordion-button collapsed bg-transparent fw-bold shadow-none link-primary" type="button" data-bs-toggle="collapse" data-bs-target="#faqCommunicationCollapse9" aria-expanded="false" aria-controls="faqCommunicationCollapse9">
                Training & Onboarding
              </button>
            </h2>
            <div id="faqCommunicationCollapse9" class="accordion-collapse collapse" aria-labelledby="faqCommunicationHeading9">
              <div class="accordion-body">
                <ul>
                  <li>Do you offer training for campus communication leads?</li>
                  <li>How can I schedule a consultation with the main Communication Ministry?</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- General Contact & Follow-up -->
          <div class="accordion-item bg-transparent border-bottom py-3">
            <h2 class="accordion-header" id="faqCommunicationHeading10">
              <button class="accordion-button collapsed bg-transparent fw-bold shadow-none link-primary" type="button" data-bs-toggle="collapse" data-bs-target="#faqCommunicationCollapse10" aria-expanded="false" aria-controls="faqCommunicationCollapse10">
                General Contact & Follow-up
              </button>
            </h2>
            <div id="faqCommunicationCollapse10" class="accordion-collapse collapse" aria-labelledby="faqCommunicationHeading10">
              <div class="accordion-body">
                <ul>
                  <li>What’s the best way to follow up on a previous request?</li>
                  <li>Where can I submit general questions or feedback?</li>
                </ul>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>

</section>
@endsection
