import image1 from 'assets/img/chat/1.png';
import image2 from 'assets/img/chat/2.png';
import image3 from 'assets/img/chat/3.png';
import image4 from 'assets/img/chat/4.png';
import image5 from 'assets/img/chat/5.png';
import image6 from 'assets/img/chat/6.png';
import image7 from 'assets/img/chat/7.png';
import image8 from 'assets/img/chat/8.png';
import image9 from 'assets/img/chat/9.png';
import image10 from 'assets/img/chat/10.png';
import image11 from 'assets/img/chat/11.png';
import image12 from 'assets/img/chat/12.png';
import image13 from 'assets/img/chat/13.png';
import image14 from 'assets/img/chat/14.png';
import team20 from 'assets/img/team/avatar.webp';
import team29 from 'assets/img/team/avatar.webp';
import team30 from 'assets/img/team/avatar.webp';
import team25 from 'assets/img/team/avatar.webp';
import team15 from 'assets/img/team/avatar.webp';
import team59 from 'assets/img/team/avatar.webp';
import team1 from 'assets/img/team/avatar.webp';
import team6 from 'assets/img/team/avatar.webp';
import team60 from 'assets/img/team/avatar.webp';
import team57 from 'assets/img/team/avatar.webp';
import { FileAttachment } from 'components/common/AttachmentPreview';
import { IconProp } from '@fortawesome/fontawesome-svg-core';
import {
  faFaceSmile,
  faPenToSquare,
  faReply,
  faShare,
  faTrash
} from '@fortawesome/free-solid-svg-icons';

export interface Message {
  id: number;
  type: 'sent' | 'received';
  message?: string;
  time: string;
  readAt: Date | string | null;
  attachments?: { images?: string[]; file?: FileAttachment };
  seen?: boolean;
}
export interface User {
  id: number;
  avatar?: string;
  status: 'online' | 'offline';
  name: string;
}
export interface Conversation {
  id: number;
  user: User;
  messages: Message[];
  unreadMessages?: number;
}

export type MessageActionType = {
  icon: IconProp;
  label: string;
};

export const supportChat: Conversation = {
  id: 1,
  user: { id: 1, avatar: team30, status: 'online', name: 'Шарука Ниджибум' },
  messages: []
};

export const suggestions: string[] = [
  'Мне нужна помощь',
  'Я не могу оформить повторный вызов',
  'Как разместить вызов?'
];

export const conversations: Conversation[] = [
  {
    id: 1,
    user: { id: 1, avatar: team20, status: 'online', name: 'Шарука Ниджибум' },
    messages: [
      {
        id: 1,
        type: 'received',
        message:
          'Петр Питер собрал пек маринованного перца. Пек маринованного перца собрал Петр Питер.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 2,
        type: 'sent',
        message:
          'Если Петр Питер собрал пек маринованного перца, где пек маринованного перца, который собрал Петр Питер?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 3,
        type: 'sent',
        message: 'Да, в организационной структуре',
        attachments: { images: [image1] },
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 4,
        type: 'received',
        message: 'Эдди отредактировал это.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 5,
        type: 'sent',
        message: 'Вилли действительно устал.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 6,
        type: 'received',
        message:
          'Ты знаешь Нью-Йорк, тебе нужен Нью-Йорк, ты знаешь, что тебе нужен уникальный Нью-Йорк.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 7,
        type: 'sent',
        message: 'Это сообщение от вас',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 8,
        type: 'received',
        message:
          'У меня встреча без четверти восемь; увидимся у ворот, так что не опаздывай.',
        time: 'Вчера, 10:00',
        readAt: null
      }
    ],
    unreadMessages: 1
  },
  {
    id: 2,
    user: {
      id: 2,
      avatar: team29,
      status: 'offline',
      name: 'Урито Нисемуно'
    },
    messages: [
      {
        id: 1,
        type: 'received',
        message:
          'Нед Нотт был застрелен, а Сэм Шотт не был. Так что лучше быть Шоттом, чем Ноттом.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 2,
        type: 'sent',
        attachments: {
          images: [
            image12,
            image13,
            image2,
            image3,
            image4,
            image5,
            image6,
            image7,
            image8,
            image9,
            image10,
            image11
          ]
        },
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 3,
        type: 'sent',
        message:
          'Некоторые говорят, что Нотт не был застрелен. Но Шотт говорит, что он застрелил Нотта.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 4,
        type: 'received',
        message:
          'Но Шотт говорит, что он застрелил Нотта. Либо выстрел, который Шотт сделал в Нотта, не попал, либо Нотт был застрелен.',
        time: 'Вчера, 10:00',
        readAt: null
      },
      {
        id: 5,
        type: 'sent',
        message: 'Если выстрел Шотта попал в Нотта, то Нотт был застрелен.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 6,
        type: 'received',
        message:
          'Но если выстрел Шотта попал в Шотта, то Шотт был застрелен, а не Нотт.',
        time: 'Вчера, 10:00',
        readAt: null
      },
      {
        id: 7,
        type: 'sent',
        message: 'Однако выстрел Шотта попал не в Шотта, а в Нотта.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 8,
        type: 'received',
        time: 'Вчера, 10:00',
        readAt: null,
        attachments: {
          file: {
            name: 'Неподдерживаемый формат файла.mad',
            size: '11.13 KB',
            date: '2 дек 2011',
            format: 'mad'
          }
        }
      }
    ],
    unreadMessages: 3
  },
  {
    id: 3,
    user: {
      id: 3,
      avatar: team30,
      status: 'online',
      name: 'Сян Ледипесипанг'
    },
    messages: [
      {
        id: 1,
        type: 'received',
        message:
          'Древесная жаба полюбила самку-жабу, которая жила на дереве. Он был двухпалой древесной жабой, а она была трехпалой жабой. Двухпалая древесная жаба пыталась завоевать сердце трехпалой самки-жабы, ибо двухпалая древесная жаба любила землю, по которой ступала трехпалая древесная жаба. Но двухпалая древесная жаба пыталась напрасно; он не мог угодить её прихоти. Из своего древесного убежища, своей трехпалой силой, самка-жаба наложила на него вето.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      }
    ]
  },
  {
    id: 4,
    user: {
      id: 4,
      avatar: team25,
      status: 'online',
      name: 'Абшини Типано'
    },
    messages: [
      {
        id: 1,
        type: 'received',
        message: 'Привет, я Доктор Тройной А! Чем могу помочь?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 2,
        type: 'received',
        message:
          'Что бежит, но никогда не ходит. Бормочет, но никогда не говорит. Имеет кровать, но никогда не спит. И имеет рот, но никогда не ест?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 3,
        type: 'received',
        message:
          'Река. Но у меня есть голова и хвост, которые никогда не встретятся. Иметь слишком много меня всегда приятно. Что я?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 4,
        type: 'sent',
        message: 'Монета, или что?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 5,
        type: 'sent',
        message:
          'Ну скажи мне, что я, если меня никогда нельзя бросить, но можно поймать. Способы потерять меня всегда ищут.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 6,
        type: 'received',
        message:
          'Простуда. Но что ты выбрасываешь, когда хочешь использовать, но забираешь, когда не хочешь использовать?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 7,
        type: 'sent',
        message: 'Якорь, верно?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      }
    ],
    unreadMessages: 3
  },
  {
    id: 5,
    user: {
      id: 5,
      avatar: team15,
      status: 'online',
      name: 'Ненко Нимитанип'
    },
    messages: [
      {
        id: 1,
        type: 'sent',
        message:
          'Когда врач лечит врача, лечит ли врач, который лечит, так, как хочет быть вылеченным врач, которого лечат, или врач, который лечит, лечит так, как он хочет лечить?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      }
    ]
  },
  {
    id: 6,
    user: {
      id: 6,
      avatar: team59,
      status: 'online',
      name: 'Шанито Бистроглини'
    },
    messages: [
      {
        id: 1,
        type: 'sent',
        message:
          'Когда врач лечит врача, лечит ли врач, который лечит, так, как хочет быть вылеченным врач, которого лечат, или врач, который лечит, лечит так, как он хочет лечить?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 2,
        type: 'received',
        message: 'Ну… проверь прикрепленный файл для ответа, чувак!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      }
    ]
  },
  {
    id: 7,
    user: {
      id: 7,
      status: 'online',
      name: 'Мистони Трепалнано'
    },
    messages: [
      {
        id: 1,
        type: 'received',
        message:
          'Мистер Си владел пилой. А мистер Соар владел качелями. Теперь пила Си распилила качели Соара до того, как Соар увидел Си, что сделало Соара раздраженным.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 2,
        type: 'sent',
        message:
          'Если бы Соар увидел пилу Си до того, как Си распилил качели Соара, пила Си не распилила бы качели Соара.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 3,
        type: 'received',
        message:
          'Так что пила Си распилила качели Соара. Но было грустно видеть Соара таким раздраженным только из-за того, что пила Си распилила качели Соара.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      }
    ]
  },
  {
    id: 8,
    user: {
      id: 8,
      avatar: team1,
      status: 'online',
      name: 'Зогиди Лишанг'
    },
    messages: [
      {
        id: 1,
        type: 'sent',
        message:
          'Проницательная Полли Перкинс купила продукт Питера и продавала соленья, чтобы получить хорошую прибыль!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 2,
        type: 'received',
        message:
          'Я разрезал простыню, простыню я разрезал, и на разрезанной простыне я сижу.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 3,
        type: 'sent',
        message: 'Зеленые стеклянные шары светятся зеленым.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 4,
        type: 'sent',
        message:
          'Гениальные игуаны импровизируют сложную импровизацию на невозможных-непрактичных инструментах.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 5,
        type: 'received',
        message:
          'Быстрые храбрые бригадиры размахивали широкими яркими клинками, мушкетами и дубинками—плохо балансируя ими.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      }
    ]
  },
  {
    id: 9,
    user: {
      id: 9,
      avatar: team6,
      status: 'online',
      name: 'Нонтепорано Лепат'
    },
    messages: [
      {
        id: 1,
        type: 'received',
        message:
          'какую книгу вы помните, чтобы иметь самое длинное возможное предложение?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 2,
        type: 'sent',
        message: 'Не знаю! Думаю, нелегко читать и считать слова!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 3,
        type: 'received',
        message:
          '«Отверженные» Виктора Гюго содержат предложение из 823 слов, и, надеюсь, никто другой не напишет длиннее, чтобы побить рекорд.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 4,
        type: 'sent',
        message:
          'Ну… я знаю уникально длинное название. Ты знаешь, у кого оно есть?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 5,
        type: 'received',
        message: 'Ну…нет?',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 6,
        type: 'sent',
        message:
          'Самое длинное название книги состоит из 3777 слов. Я не хочу записывать это для тебя, иди найди!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      }
    ]
  },
  {
    id: 10,
    user: {
      id: 10,
      avatar: team60,
      status: 'online',
      name: 'Джессика Болл'
    },
    messages: [
      {
        id: 1,
        type: 'received',
        message: 'Также, что?!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 2,
        type: 'sent',
        message:
          'Но на самом деле люди более смертоносны для акул, чем акулы для людей. Люди убивают около 100 миллионов акул в год!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 3,
        type: 'received',
        message:
          'Трудно поверить, но это правда. Акулы убивают в среднем 5 человек в год, в то время как коровы убивают в среднем 22 человека в год.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 4,
        type: 'sent',
        message: 'Чтто?!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 5,
        type: 'received',
        message: 'Коровы убивают больше людей, чем акулы!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      }
    ]
  },
  {
    id: 11,
    user: {
      id: 11,
      avatar: team57,
      status: 'online',
      name: 'Харли Браун'
    },
    messages: [
      {
        id: 1,
        type: 'received',
        message:
          'Облака в центре Млечного Пути пахнут ромом, имеют вкус малины и полны алкоголя!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 2,
        type: 'sent',
        message: 'Правда?!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 3,
        type: 'received',
        message:
          'О да! Там достаточно алкоголя, чтобы снабжать каждого человека на планете 300 000 пинтами пива в день в течение следующего миллиарда лет!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 4,
        type: 'sent',
        message:
          'И знаешь, что я слышал? Нептун совершил только один оборот вокруг Солнца с момента его открытия!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 5,
        type: 'received',
        message: 'И Солнце теряет миллиард килограммов в секунду.',
        time: 'Вчера, 10:00',
        readAt: new Date()
      },
      {
        id: 6,
        type: 'sent',
        message:
          'ОМГ! Мне следует оставить своего диетолога и попросить совета у Бога Солнца!',
        time: 'Вчера, 10:00',
        readAt: new Date()
      }
    ]
  }
];

export const attachments = [
  {
    image: image2
  },
  {
    image: image3
  },
  {
    image: image4
  },
  {
    image: image5
  },
  {
    image: image6
  },
  {
    image: image7
  },
  {
    image: image8
  },
  {
    image: image9
  },
  {
    image: image10
  },
  {
    image: image11
  },
  {
    image: image12
  },
  {
    image: image13
  },
  {
    image: image14
  }
];

export const files: FileAttachment[] = [
  {
    name: 'Federico_salsaniuella_godarf_design.zip',
    size: '53.34 MB',
    date: '8 дек 2011',
    format: 'zip'
  },
  {
    name: 'Restart_lyf.bat',
    size: '11.13 KB',
    date: '2 дек 2011',
    format: 'bat'
  },
  {
    name: 'Поддельный lorem ipsum.txt',
    size: '11.13 KB',
    date: '2 дек 2011',
    format: 'txt'
  },
  {
    name: 'Неподдерживаемый формат файла.mad',
    size: '11.13 KB',
    date: '2 дек 2011',
    format: 'mad'
  }
];

export const actions: MessageActionType[] = [
  {
    icon: faTrash,
    label: 'Удалить'
  },
  {
    icon: faReply,
    label: 'Ответить'
  },
  {
    icon: faPenToSquare,
    label: 'Редактировать'
  },
  {
    icon: faShare,
    label: 'Поделиться'
  },
  {
    icon: faFaceSmile,
    label: 'Эмодзи'
  }
];
