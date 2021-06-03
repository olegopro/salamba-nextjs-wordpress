import * as React from "react";

function SvgLogo(props) {
  return (
    <svg
      width={45}
      height={45}
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      {...props}
    >
      <path
        d="M26.477 1.275C16.634 3.232 8.855 12.211 8.855 22.5c0 10.29 7.779 19.268 17.622 21.225C36.32 41.768 44.1 32.789 44.1 22.5c0-10.29-7.779-19.268-17.622-21.225z"
        fill="#2D9CDB"
      />
      <path
        d="M21.775 21.973V14.94h4.219v-2.988h-7.032v7.031h-7.03v7.032h2.812v-4.043h7.03z"
        fill="#fff"
      />
      <path
        d="M28.806 18.984h-2.812v-4.043h-4.22v7.032h-7.03v4.043h4.218V33.047h7.032v-7.031h7.03v-7.032h-4.218z"
        fill="#E3F2FF"
      />
      <path
        d="M11.69 9.053a.88.88 0 10-.001-1.759.88.88 0 000 1.759z"
        fill="#000"
      />
      <path
        d="M22.5 0C10.373 0 0 10.354 0 22.5 0 34.63 10.355 45 22.5 45 34.627 45 45 34.646 45 22.5 45 10.37 34.645 0 22.5 0zm0 43.242c-11.243 0-20.742-9.499-20.742-20.742S11.257 1.758 22.5 1.758 43.242 11.257 43.242 22.5 33.743 43.242 22.5 43.242z"
        fill="#000"
      />
      <path
        d="M22.5 3.78c-2.818 0-5.8.65-8.18 1.784a.879.879 0 00.757 1.587c2.152-1.026 4.857-1.614 7.423-1.614 9.195 0 16.963 7.768 16.963 16.963S31.695 39.463 22.5 39.463 5.537 31.695 5.537 22.5c0-4.2 1.54-8.34 4.228-11.357a.879.879 0 10-1.313-1.17C5.482 13.31 3.779 17.876 3.779 22.5c0 10.171 8.535 18.72 18.721 18.72 10.171 0 18.72-8.534 18.72-18.72 0-10.171-8.534-18.72-18.72-18.72z"
        fill="#000"
      />
      <path
        d="M26.016 11.074h-7.032a.879.879 0 00-.879.88v6.151h-6.152a.879.879 0 00-.879.88v7.03c0 .486.394.88.88.88h6.151v6.152c0 .485.394.879.88.879h7.03a.879.879 0 00.88-.88v-6.151h6.152a.879.879 0 00.879-.88v-7.03a.879.879 0 00-.88-.88h-6.151v-6.152a.879.879 0 00-.88-.879zm6.152 8.79v5.273h-6.152a.879.879 0 00-.88.879v6.152h-5.273v-6.152a.879.879 0 00-.879-.88h-6.152v-5.273h6.152a.879.879 0 00.88-.879v-6.152h5.273v6.152c0 .486.393.88.879.88h6.152z"
        fill="#000"
      />
    </svg>
  );
}

export default SvgLogo;