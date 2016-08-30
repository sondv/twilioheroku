/**
 * システムサービス
 */
export default class SystemService {
	static $inject = ['moment'];


	/**
	 * コンストラクタ
	 *
	 * @param moment
	 */
	constructor(moment) {
		this.moment = moment;
	}

	/**
	 * 文字列の日時をDate型へ変換する
	 *
	 * @param obj
	 * @returns {*}
	 */
	dateStringToISO8601(obj) {
		for (var i in obj) {
			if (Object.prototype.toString.call(obj[i]).toString() === '[object Object]'
				|| Object.prototype.toString.call(obj[i]).toString() === '[object Array]') {
				obj[i] = this.dateStringToISO8601(obj[i]);
			}

			if (Object.prototype.toString.call(obj[i]).toString() === '[object String]'
				&& obj[i].match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.000Z$/)) {
				obj[i] = this.moment(obj[i]).toDate();
			}
		}
		return obj;
	}

	/**
	 * 日付を加算
	 *
	 * @param date
	 * @param add
	 * @returns {Date|*}
	 */
	addDate(date, add) {
		if (date == null || date === '') {
			return;
		}
		return this.moment(date).add(add, 'd').toDate();
	}

	/**
	 * 日付を整形
	 *
	 * @param date
	 * @param format
	 * @returns {*}
	 */
	formatDate(date, format = 'YYYY-MM-DD') {
		if (date == null || date === '') {
			return;
		}
		return this.moment(date).format(format);
	}

	/**
	 * 郵便番号を整形
	 */
    formatPostCode(value) {
		if (value == null || value === '') {
			return '';
		}

        // 数字以外の文字がある場合削除する
        value = this.replace(value, '[^0-9]+', '', 'g');

        // 数字を取得する
        var numMatch = this.match(value, '[0-9]+', 'g');
        var code = '';
        for (var idx in numMatch) {
            code = ''.concat(code, numMatch[idx]);
        }
        var codeLen = code.length;
        var firstLen = 3;
        if (codeLen < 3) {
            firstLen = codeLen;
        }
        var firstCode = code.substr(0, firstLen);

        var secondCode = '';
        var secondLen = codeLen - firstLen;
        if (secondLen < 0) {
            secondLen = 0;
            value = firstCode;
        } else if (secondLen > 4) {
            secondLen = 4;
        }
        if (secondLen > 0) {
            secondCode = code.substr(3, secondLen);
            value = firstCode + '-' + secondCode;
        }
        return value;
    }

	/**
	 * 郵便番号を整形
	 */
    formatPhone(value) {
		if (value == null || value === '') {
			return '';
		}

		let codes = ['01267', '01372', '01374', '0137', '01377', '0138', '01392', '01558', '01564', '01586', '01587', '01632', '01634', '01635', '01648', '01654', '01655', '01656', '01658', '01397', '01398', '01456', '01457', '01466', '01547', '04992', '04994', '04996', '04998', '05769',  '05979', '08387', '08388', '08396', '08477', '08512', '08514', '07468', '09496', '09802', '09912', '09913', '09969', '0123', '0124', '0125', '0126', '0133', '0134', '0135', '0136', '0152', '0153', '0154', '0155', '0156', '0157', '0158', '0162', '0163', '0164', '0165', '0166', '0167', '0172', '0173', '0174', '0175', '0176', '0178', '0179', '0182', '0183', '0184', '0185', '0186', '0187', '0191', '0192', '0193', '0194', '0195', '0139', '0142', '0143', '0144', '0145', '0146', '0197', '0198', '0220', '0223', '0224', '0225', '0226', '0228', '0229', '0233', '0234', '0235', '0237', '0238', '0240', '0241', '0242', '0243', '0244', '0246', '0247', '0248', '0250', '0254', '0255', '0256', '0257', '0258', '0259', '0260', '0261', '0263', '0264', '0265', '0266', '0267', '0268', '0269', '0270', '0274', '0276', '0277', '0278', '0279', '0280', '0282', '0283', '0284', '0285', '0287', '0288', '0289', '0291', '0293', '0294', '0295', '0296', '0297', '0299', '0422', '0428', '0436', '0438', '0439', '0460', '0463', '0465', '0466', '0467', '0470', '0475', '0476', '0478', '0479', '0480', '0493', '0494', '0495', '0531', '0532', '0533', '0536', '0537', '0538', '0539', '0544', '0545', '0547', '0548', '0550', '0551', '0553', '0554', '0555', '0556', '0557', '0558', '0561', '0562', '0563', '0564', '0565', '0566', '0567', '0568', '0569', '0572', '0573', '0574', '0575', '0576', '0577', '0578', '0581', '0584', '0585', '0586', '0587', '0594', '0595', '0596', '0597', '0598', '0599', '0721', '0725', '0735', '0736', '0737', '0738', '0739', '0740', '0742', '0743', '0744', '0745', '0746', '0747', '0748', '0749', '0761', '0763', '0765', '0766', '0767', '0768', '0770', '0771', '0772', '0773', '0774', '0776', '0778', '0779', '0790', '0791', '0794', '0795', '0796', '0797', '0798', '0799', '0820', '0823', '0824', '0826', '0827', '0829', '0833', '0834', '0835', '0836', '0837', '0838', '0845', '0846', '0847', '0848', '0852', '0853', '0854', '0855', '0856', '0857', '0858', '0859', '0863', '0865', '0866', '0867', '0868', '0869', '0875', '0877', '0879', '0880', '0883', '0884', '0885', '0887', '0889', '0892', '0893', '0894', '0895', '0896', '0897', '0898', '0920', '0930', '0940', '0942', '0943', '0944', '0946', '0947', '0948', '0949', '0950', '0952', '0954', '0955', '0956', '0957', '0959', '0964', '0965', '0966', '0967', '0968', '0969', '0972', '0973', '0974', '0977', '0978', '0979', '0980', '0982', '0983', '0984', '0985', '0986', '0987', '0993', '0994', '0995', '0996','0997', '050', '070', '080', '090', '011', '018', '017', '015', '019', '022', '023', '024', '025', '026', '027', '028', '029', '042', '043', '044', '045', '046', '047', '048', '049', '052', '053', '054', '055', '058', '059', '072', '073', '077', '075', '076', '078', '079', '082', '083', '084', '086', '087', '088', '089', '092', '093', '095', '096', '097', '098', '099', '03', '04', '06'];

		// 数字のみにする
		value = value.replace(/[^0-9]/g, '').toString();

		// 国内プレフィックスを付与
		if (value.substr(0, 1) !== '0') {
			value = '0' + value;
		}

		// 電話番号が0始まりの10,11桁か
		if (value.match(/^0[0-9]{9,10}$/) === false) {
			return value;
		}

		// 市外局番、市内局番、加入者番号に分割する
		var phone = [];
		var keepGoing = true;
		angular.forEach(codes, function(code) {
			if (keepGoing === false || value.startsWith(code) === false) {
				return;
			}
			// 市外局番
			phone.push(code);
			// 市内局番
			phone.push(value.substr(code.length, value.length - code.length - 4));
			// 加入者番号
			phone.push(value.substr(-4));

			keepGoing = false;
		});
		return phone.join('-');
    }

	/**
	 * リストをオンオフ
	 *
	 * @param item
	 * @param list
	 */
	toggleList(item, list) {
		if (Array.isArray(list) === false) {
			list = [];
		}
		let idx = list.indexOf(item);
		if (idx > -1) {
			list.splice(idx, 1);
		} else {
			list.push(item);
		}
	}

	/**
	 * リストに存在するかどうか
	 *
	 * @param item
	 * @param list
	 * @returns {boolean}
	 */
	existsList(item, list) {
		if (Array.isArray(list) === false) {
			return false;
		}
		return list.indexOf(item) > -1;
	}

	/**
	 * match
	 *
	 * @param value 値
	 * @param pattern 正規表現
	 * @param flags タイプ(i：ignore case　g：global　m：multiline)
	 */
	match(value, pattern, flags) {
		//値が有効でない場合は処理を抜ける
		if (value == null || value.length === 0) {
			return [];
		}

		if (flags == null) {
			flags = 'i';
		}
		//値が有効な場合
		var result = value.match(new RegExp(pattern, flags));
		//検索結果がない場合処理を抜ける
		if (result === null || result.length === 0) {
			return [];
		}
		return result;
	}

	/**
	 * replace
	 *
	 * @param value 値
	 * @param pattern 正規表現
	 * @param replaceValue 置換文字列
	 * @param flags タイプ(i：ignore case　g：global　m：multiline)
	 */
	replace(value, pattern, replaceValue, flags) {
		if (flags == null) {
			flags = 'i';
		}

		//条件に合うデータが存在するか。
		var matchValue = this.match(value, pattern, flags);
		//置換する
		if (matchValue.length > 0) {
			return value.replace(new RegExp(pattern, flags), replaceValue);
		}
		return value;
	}
}
